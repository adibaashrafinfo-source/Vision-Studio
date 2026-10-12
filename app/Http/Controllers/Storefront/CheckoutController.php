<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Mail\OrderAdminNotification;
use App\Mail\OrderPlaced;
use App\Models\Customer;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): RedirectResponse|View
    {
        if (empty(Cart::items())) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $subtotal = Cart::subtotal();

        return view('storefront.checkout', [
            'items' => Cart::items(),
            'subtotal' => $subtotal,
            'discount' => Cart::discount(),
            'shipping' => $this->shippingFor($subtotal),
            'customer' => auth('web')->user(),
            'methods' => $this->enabledPaymentMethods(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (empty(Cart::items())) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $allowed = array_keys($this->enabledPaymentMethods());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'address_line' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'payment_method' => ['required', 'in:'.implode(',', $allowed)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $items = Cart::items();
        $subtotal = Cart::subtotal();
        $shipping = $this->shippingFor($subtotal);
        $discount = Cart::discount();
        $coupon = Cart::coupon();

        $isNewAccount = false;

        $order = DB::transaction(function () use ($data, $items, $subtotal, $shipping, $discount, $coupon, &$isNewAccount) {
            // Attach to the logged-in customer, or find/create one by email.
            // Guest-checkout accounts get a random hashed password so they can
            // later claim the account via the Forgot Password flow.
            $customer = auth('web')->user();
            if (! $customer) {
                $customer = Customer::where('email', $data['email'])->first();
                if (! $customer) {
                    $customer = Customer::create([
                        'email' => $data['email'],
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'password' => Str::random(32),
                        'status' => 'active',
                    ]);
                    $isNewAccount = true;
                }
            }

            // Keep a default address on file.
            $customer->addresses()->firstOrCreate(
                ['address_line' => $data['address_line']],
                ['city' => $data['city'], 'label' => 'Shipping', 'is_default' => true]
            );

            $order = Order::create([
                'order_no' => $this->nextOrderNo(),
                'customer_id' => $customer->id,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shipping,
                'total' => $subtotal - $discount + $shipping,
            ]);

            foreach ($items as $line) {
                $order->items()->create([
                    'product_id' => $line['id'],
                    'product_name' => $line['name'],
                    'qty' => $line['qty'],
                    'unit_price' => $line['price'],
                    'subtotal' => $line['price'] * $line['qty'],
                ]);
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $order;
        });

        Cart::clear();

        $this->sendOrderEmails($order, $isNewAccount);

        return redirect()->route('checkout.confirmation', $order)->with('status', 'Order placed successfully!');
    }

    protected function sendOrderEmails(Order $order, bool $isNewAccount): void
    {
        try {
            Mail::to($order->customer->email)->send((new OrderPlaced($order))->with(['isNewAccount' => $isNewAccount]));

            if ($adminEmail = SiteSetting::get('order_notification_email') ?: config('mail.from.address')) {
                Mail::to($adminEmail)->send(new OrderAdminNotification($order));
            }
        } catch (\Throwable $e) {
            // Never fail an order over a mail hiccup — SMTP outages happen.
            Log::warning('Order email failed for #'.$order->order_no.': '.$e->getMessage());
        }
    }

    public function confirmation(Order $order): View
    {
        $order->load('items', 'customer');

        return view('storefront.confirmation', compact('order'));
    }

    /** Payment methods enabled in admin Settings (card is always available). */
    protected function enabledPaymentMethods(): array
    {
        $methods = [];
        if (SiteSetting::get('bkash_enabled', 1)) {
            $methods['bkash'] = ['label' => 'bKash', 'sub' => 'Pay with your bKash wallet', 'color' => '#E2136E'];
        }
        if (SiteSetting::get('nagad_enabled', 1)) {
            $methods['nagad'] = ['label' => 'Nagad', 'sub' => 'Instant mobile payment', 'color' => '#EE3A24'];
        }
        $methods['card'] = ['label' => 'Visa / Mastercard / Amex', 'sub' => 'Credit & debit cards', 'color' => '#1A1F71'];
        if (SiteSetting::get('cod_enabled', 1)) {
            $methods['cod'] = ['label' => 'Cash on Delivery', 'sub' => 'Pay when you receive it', 'color' => '#0A1A42'];
        }

        return $methods;
    }

    protected function shippingFor(float $subtotal): float
    {
        $threshold = (float) (SiteSetting::get('free_shipping_threshold', 2000));
        $fee = (float) (SiteSetting::get('shipping_fee', 60));

        return ($threshold > 0 && $subtotal >= $threshold) ? 0 : $fee;
    }

    protected function nextOrderNo(): string
    {
        $seq = 1000 + Order::count() + 1;
        while (Order::where('order_no', 'VS-'.$seq)->exists()) {
            $seq++;
        }

        return 'VS-'.$seq;
    }
}

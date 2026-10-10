<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (empty(Cart::items())) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'address_line' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'payment_method' => ['required', 'in:cod,bkash,nagad,card'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $items = Cart::items();
        $subtotal = Cart::subtotal();
        $shipping = $this->shippingFor($subtotal);
        $discount = Cart::discount();
        $coupon = Cart::coupon();

        $order = DB::transaction(function () use ($data, $items, $subtotal, $shipping, $discount, $coupon) {
            // Attach to the logged-in customer, or find/create one by email.
            $customer = auth('web')->user() ?? Customer::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'phone' => $data['phone'], 'status' => 'active']
            );

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

        return redirect()->route('checkout.confirmation', $order)->with('status', 'Order placed successfully!');
    }

    public function confirmation(Order $order): View
    {
        $order->load('items', 'customer');

        return view('storefront.confirmation', compact('order'));
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

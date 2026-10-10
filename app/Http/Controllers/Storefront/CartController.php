<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('storefront.cart', [
            'items' => Cart::items(),
            'subtotal' => Cart::subtotal(),
            'discount' => Cart::discount(),
            'coupon' => Cart::coupon(),
        ]);
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'max:60']]);
        $code = strtoupper(trim($request->get('code')));

        $coupon = Coupon::where('code', $code)->where('status', 'active')->first();
        if (! $coupon) {
            return back()->withErrors(['code' => 'That coupon code is not valid.']);
        }

        Session::put('coupon', $coupon->code);

        if (! Cart::coupon()) {
            Session::forget('coupon');
            return back()->withErrors(['code' => 'This coupon cannot be applied to your current cart.']);
        }

        return back()->with('status', "Coupon {$coupon->code} applied.");
    }

    public function removeCoupon(): RedirectResponse
    {
        Session::forget('coupon');

        return back()->with('status', 'Coupon removed.');
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::active()->with('primaryImage')->findOrFail($data['product_id']);
        Cart::add($product, $data['qty'] ?? 1);

        return back()->with('status', $product->name.' added to your cart.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate(['qty' => ['required', 'integer', 'min:0', 'max:99']]);
        Cart::update($id, (int) $request->get('qty'));

        return back()->with('status', 'Cart updated.');
    }

    public function remove(int $id): RedirectResponse
    {
        Cart::remove($id);

        return back()->with('status', 'Item removed.');
    }
}

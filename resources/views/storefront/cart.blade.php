@extends('layouts.storefront')

@section('title', 'Shopping Cart')

@push('styles')<link rel="stylesheet" href="{{ asset('storefront/cart.css') }}">@endpush

@php
    use App\Support\Cart;
    $shippingThreshold = (float) (\App\Models\SiteSetting::get('free_shipping_threshold', 2000));
    $shippingFee = (float) (\App\Models\SiteSetting::get('shipping_fee', 60));
    $shipping = ($shippingThreshold > 0 && $subtotal >= $shippingThreshold) ? 0 : $shippingFee;
    $total = $subtotal - $discount + $shipping;
    $count = Cart::count();
@endphp

@section('content')
<div class="wrap">
    <div class="crumb"><a href="{{ route('home') }}">Home</a><span class="sep">/</span><span style="color:var(--text)">Shopping Cart</span></div>
    <div class="phead"><h1>Shopping Cart</h1><span class="tnum">{{ $count }} item{{ $count === 1 ? '' : 's' }}</span></div>

    @if (empty($items))
        <div style="text-align:center;padding:60px 0">
            <p class="sec-sub" style="font-size:16px;margin-bottom:18px">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Start Shopping</a>
        </div>
    @else
    <div class="cart-layout">
        <div>
            <div class="items">
                @foreach ($items as $it)
                    <div class="citem">
                        <a class="thumb" href="{{ route('product.show', $it['slug']) }}">
                            @if ($it['image'])<img class="pimg" src="{{ asset('storage/'.$it['image']) }}" alt="{{ $it['name'] }}">
                            @else<svg class="glass" viewBox="0 0 200 90"><circle cx="100" cy="45" r="30"></circle><circle cx="100" cy="45" r="8"></circle></svg>@endif
                        </a>
                        <div>
                            <h3><a href="{{ route('product.show', $it['slug']) }}" style="color:inherit">{{ $it['name'] }}</a></h3>
                            <div class="meta">{{ taka($it['price']) }} each</div>
                            <form method="POST" action="{{ route('cart.remove', $it['id']) }}">@csrf @method('DELETE')
                                <button class="rm" type="submit"><svg class="icon" viewBox="0 0 24 24"><path d="M4 7h16"></path><path d="M9 7V5h6v2"></path><path d="M6 7l1 13h10l1-13"></path></svg>Remove</button>
                            </form>
                        </div>
                        <div class="right">
                            <div class="qty">
                                <form method="POST" action="{{ route('cart.update', $it['id']) }}" style="display:inline">@csrf @method('PATCH')<input type="hidden" name="qty" value="{{ $it['qty'] - 1 }}"><button type="submit" aria-label="Decrease">−</button></form>
                                <span class="q tnum">{{ $it['qty'] }}</span>
                                <form method="POST" action="{{ route('cart.update', $it['id']) }}" style="display:inline">@csrf @method('PATCH')<input type="hidden" name="qty" value="{{ $it['qty'] + 1 }}"><button type="submit" aria-label="Increase">+</button></form>
                            </div>
                            <div class="lp tnum">{{ taka($it['price'] * $it['qty']) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('products.index') }}" class="cont"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:var(--blue)"><path d="M19 12H5"></path><path d="M11 6l-6 6 6 6"></path></svg>Continue Shopping</a>
        </div>

        <aside class="summary">
            <h3>Order Summary</h3>
            <div class="srow"><span>Subtotal</span><span class="v tnum">{{ taka($subtotal) }}</span></div>
            @if ($discount > 0)
                <div class="srow"><span>Discount ({{ $coupon->code }})</span><span class="v tnum" style="color:var(--sale)">−{{ taka($discount) }}</span></div>
            @endif
            <div class="srow"><span>Shipping {{ $shipping == 0 ? '(Free)' : '(Dhaka)' }}</span><span class="v tnum">{{ $shipping == 0 ? 'Free' : taka($shipping) }}</span></div>
            <div class="promo">
                <form method="POST" action="{{ route('cart.coupon') }}" style="display:flex;gap:8px;width:100%">@csrf
                    <input type="text" name="code" aria-label="Promo code" placeholder="Promo code" value="{{ $coupon?->code }}" style="flex:1">
                    <button type="submit">Apply</button>
                </form>
            </div>
            @error('code')<div style="color:var(--sale);font-size:12px;margin-top:6px">{{ $message }}</div>@enderror
            @if ($coupon)<form method="POST" action="{{ route('cart.coupon.remove') }}">@csrf @method('DELETE')<button class="rm" type="submit" style="margin-top:8px">Remove coupon</button></form>@endif
            <div class="stot"><span class="l">Total</span><span class="v tnum">{{ taka($total) }}</span></div>
            <div style="margin-top:18px"><a href="{{ route('checkout.index') }}" class="btn btn-primary" style="width:100%">Proceed to Checkout<svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg></a></div>
            @if ($shipping > 0 && $shippingThreshold > 0)
                <div class="ship-note"><svg class="icon sm" viewBox="0 0 24 24"><path d="M3 7h11v8H3z"></path><path d="M14 10h4l3 3v2h-7z"></path></svg>Add {{ taka($shippingThreshold - $subtotal) }} more for free shipping.</div>
            @endif
            <div class="trustmini">
                <span><svg class="icon" viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path></svg>Secure Checkout</span>
                <span><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect></svg>bKash · Nagad · COD</span>
            </div>
        </aside>
    </div>
    @endif
</div>
@endsection

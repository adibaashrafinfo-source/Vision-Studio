@extends('layouts.storefront')

@section('title', 'Order Confirmed')

@section('content')
<div class="wrap" style="padding:48px 20px">
    <div style="max-width:620px;margin:0 auto;background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:40px;text-align:center">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--surface-tint);display:flex;align-items:center;justify-content:center;margin:0 auto 18px">
            <svg class="icon lg" viewBox="0 0 24 24" style="stroke:var(--blue);width:30px;height:30px"><path d="M20 6L9 17l-5-5"></path></svg>
        </div>
        <h1 style="font-size:28px;margin-bottom:8px">Thank you for your order!</h1>
        <p class="sec-sub">Your order <strong style="color:var(--blue)">#{{ $order->order_no }}</strong> has been placed and is now being processed. We'll be in touch with updates.</p>

        <div style="text-align:left;margin:26px 0;border:1px solid var(--line);border-radius:14px;padding:18px">
            @foreach ($order->items as $item)
                <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:14px"><span>{{ $item->product_name }} <span style="color:var(--muted)">× {{ $item->qty }}</span></span><span class="tnum">{{ taka($item->subtotal) }}</span></div>
            @endforeach
            <div style="border-top:1px solid var(--line);margin-top:8px;padding-top:12px">
                <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--muted);padding:3px 0"><span>Subtotal</span><span class="tnum">{{ taka($order->subtotal) }}</span></div>
                @if ($order->discount > 0)<div style="display:flex;justify-content:space-between;font-size:13px;color:var(--sale);padding:3px 0"><span>Discount</span><span class="tnum">−{{ taka($order->discount) }}</span></div>@endif
                <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--muted);padding:3px 0"><span>Shipping</span><span class="tnum">{{ $order->shipping_fee == 0 ? 'Free' : taka($order->shipping_fee) }}</span></div>
                <div style="display:flex;justify-content:space-between;font-weight:700;font-size:16px;padding:8px 0 0"><span>Total</span><span class="tnum">{{ taka($order->total) }}</span></div>
            </div>
        </div>

        <p class="sec-sub" style="margin-bottom:22px">Payment: {{ ucfirst($order->payment_method) }} · {{ ucfirst($order->payment_status) }}</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
            <a href="{{ route('products.index') }}" class="btn btn-ghost">Continue Shopping</a>
            @auth('web')<a href="{{ route('account') }}" class="btn btn-primary">View My Orders</a>@endauth
        </div>
    </div>
</div>
@endsection

@extends('layouts.storefront')

@section('title', 'My Wishlist')

@push('styles')<link rel="stylesheet" href="{{ asset('storefront/category.css') }}">@endpush

@section('content')
<div class="wrap" style="padding:32px 20px">
    <div class="crumb">
        <a href="{{ route('home') }}">Home</a><span class="sep">/</span>
        <span style="color:var(--text)">Wishlist</span>
    </div>

    <div class="cat-hero">
        <div>
            <div class="eyebrow">Your saved items</div>
            <h1>My Wishlist</h1>
            <p>Products you've saved for later. Add them to your cart anytime.</p>
        </div>
        <span class="count-chip tnum">{{ $products->count() }} {{ Str::plural('item', $products->count()) }}</span>
    </div>

    @if ($products->isEmpty())
        <div style="text-align:center;padding:60px 20px;background:var(--surface);border:1px solid var(--line);border-radius:18px">
            <div style="width:64px;height:64px;border-radius:50%;background:var(--surface-tint);display:flex;align-items:center;justify-content:center;margin:0 auto 18px">
                <svg class="icon lg" viewBox="0 0 24 24" style="stroke:var(--muted);width:28px;height:28px"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"></path></svg>
            </div>
            <h2 style="margin:0 0 8px">Your wishlist is empty</h2>
            <p class="sec-sub" style="margin-bottom:20px">Save items you love by tapping the ♥ icon on any product.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Browse products</a>
        </div>
    @else
        <div class="prod-grid">
            @foreach ($products as $product)
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    @endif
</div>
@endsection

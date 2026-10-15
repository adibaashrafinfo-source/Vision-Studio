@php
    $reviewsCount = $product->relationLoaded('approvedReviews') ? $product->approvedReviews->count() : $product->approved_reviews_count ?? null;
    $rating = $product->relationLoaded('approvedReviews') && $product->approvedReviews->count()
        ? round($product->approvedReviews->avg('rating'), 1)
        : ($product->approved_reviews_avg_rating ? round($product->approved_reviews_avg_rating, 1) : null);
    $discount = $product->discount_percent;
    $inWishlist = \App\Support\Wishlist::has($product->id);
@endphp
<div class="card" x-data="{}">
    <form method="POST" action="{{ route('wishlist.toggle', $product) }}" style="position:absolute;top:10px;right:10px;z-index:2;margin:0">
        @csrf
        <button class="heart" type="submit" aria-label="{{ $inWishlist ? 'Remove from' : 'Add to' }} wishlist" style="background:{{ $inWishlist ? 'var(--sale)' : 'transparent' }};color:{{ $inWishlist ? '#fff' : 'inherit' }}"><svg class="icon" viewBox="0 0 24 24" style="{{ $inWishlist ? 'fill:#fff;stroke:#fff' : '' }}"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"></path></svg></button>
    </form>
    @if ($discount)
        <span class="ptag" style="background:var(--sale)">-{{ $discount }}%</span>
    @elseif ($product->featured)
        <span class="ptag">Bestseller</span>
    @endif
    <div class="img">
        <a href="{{ route('product.show', $product) }}" aria-label="{{ $product->name }}">
            @if ($product->primaryImage)
                <img class="pimg" src="{{ asset('storage/'.$product->primaryImage->image_path) }}" alt="{{ $product->name }}">
            @else
                <svg class="glass" viewBox="0 0 200 90"><circle cx="100" cy="45" r="30"></circle><circle cx="100" cy="45" r="8"></circle></svg>
            @endif
        </a>
        <button type="button" class="quickview-btn" @click="window.dispatchEvent(new CustomEvent('quick-view', {detail: {{ Illuminate\Support\Js::from([
            'name' => $product->name,
            'image' => $product->primaryImage ? asset('storage/'.$product->primaryImage->image_path) : null,
            'price' => (float) $product->price,
            'comparePrice' => $product->compare_price ? (float) $product->compare_price : null,
            'category' => $product->category?->name ?? 'Eyewear',
            'rating' => $rating ?? 5.0,
            'reviewsCount' => $reviewsCount ?? 0,
            'url' => route('product.show', $product),
            'productId' => $product->id,
            'inStock' => $product->stock_qty > 0,
        ]) }}}))">
            <svg class="icon sm" viewBox="0 0 24 24"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            Quick View
        </button>
    </div>
    <div class="body">
        <span class="cat">{{ $product->category?->name ?? 'Eyewear' }}</span>
        <h3><a href="{{ route('product.show', $product) }}" style="color:inherit">{{ $product->name }}</a></h3>
        <div class="rating"><svg class="icon" viewBox="0 0 24 24"><path d="M12 3l2.6 5.5 6 .8-4.3 4.2 1 6-5.3-2.9-5.3 2.9 1-6L3.4 9.3l6-.8z"></path></svg><b>{{ $rating ?? '5.0' }}</b>({{ $reviewsCount ?? 0 }})</div>
        <div class="foot">
            <span>
                <span class="price-now tnum">{{ taka($product->price) }}</span>
                @if ($discount)<span class="price-was tnum">{{ taka($product->compare_price) }}</span>@endif
            </span>
            <form method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button class="add-btn" type="submit" aria-label="Add {{ $product->name }} to cart"><svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg></button>
            </form>
        </div>
    </div>
</div>

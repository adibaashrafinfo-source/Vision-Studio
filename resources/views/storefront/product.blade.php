@extends('layouts.storefront')

@section('title', $product->name)
@section('metaDescription', Str::limit($product->short_desc ?: $product->long_desc ?: $product->name.' available at '.config('app.name').'.', 160))
@section('ogType', 'product')

@push('styles')<link rel="stylesheet" href="{{ asset('storefront/product.css') }}">@endpush

@php
    $images = $product->images->map(fn ($i) => asset('storage/'.$i->image_path))->values();
    if ($images->isEmpty()) { $images = collect([asset('images/storefront/88fb23574ac9e31842ba8408f13cc0ad.png')]); }
    $variantGroups = $product->variants->groupBy('name');
    $inStock = $product->stock_qty > 0;
@endphp

@section('content')
<div class="wrap" x-data="productPage({{ (int) $product->price }}, {{ Illuminate\Support\Js::from($images) }})">
    <div class="crumb">
        <a href="{{ route('home') }}">Home</a><span class="sep">/</span>
        @if ($product->category)<a href="{{ route('category.show', $product->category) }}">{{ $product->category->name }}</a><span class="sep">/</span>@endif
        <span style="color:var(--text)">{{ $product->name }}</span>
    </div>

    <div class="pd">
        <div class="gallery">
            <div class="main-img" @mousemove="!showVideo && zoomMove($event)" @mouseenter="!showVideo && (zooming=true)" @mouseleave="zooming=false">
                @if ($product->discount_percent)<span class="off">-{{ $product->discount_percent }}%</span>@endif
                <template x-if="!showVideo">
                    <img class="pimg zoomimg" :src="images[current]" alt="{{ $product->name }}" style="width:80%;height:80%" :style="zooming ? `transform:scale(2);transform-origin:${zoomX}% ${zoomY}%` : ''">
                </template>
                @if ($product->video_path)
                <template x-if="showVideo">
                    <video class="pimg" src="{{ asset('storage/'.$product->video_path) }}" style="width:100%;height:100%" controls autoplay></video>
                </template>
                @endif
            </div>
            @if ($images->count() > 1 || $product->video_path)
            <div class="thumbs">
                @foreach ($images as $i => $img)
                    <button class="thumb" :class="(!showVideo && current==={{ $i }}) && 'on'" @click="showVideo=false;current={{ $i }}" aria-label="View {{ $i+1 }}"><img class="pimg" src="{{ $img }}" alt=""></button>
                @endforeach
                @if ($product->video_path)
                    <button class="thumb" :class="showVideo && 'on'" @click="showVideo=true" aria-label="Play product video" style="display:flex;align-items:center;justify-content:center;background:var(--navy)">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3l16 9-16 9V3z"></path></svg>
                    </button>
                @endif
            </div>
            @endif
        </div>

        <div class="pinfo">
            <span class="cat">{{ $product->category?->name }}@if ($product->brand) · {{ $product->brand }}@endif</span>
            <h1>{{ $product->name }}</h1>
            <div class="prow">
                <span class="stars"><svg class="icon" viewBox="0 0 24 24"><path d="M12 3l2.6 5.5 6 .8-4.3 4.2 1 6-5.3-2.9-5.3 2.9 1-6L3.4 9.3l6-.8z"></path></svg><b>{{ $avgRating ?: '5.0' }}</b> ({{ $product->approvedReviews->count() }} reviews)</span>
                <span class="instock"><span class="dot" style="{{ $inStock ? '' : 'background:var(--sale)' }}"></span>{{ $inStock ? 'In Stock' : 'Out of Stock' }}</span>
            </div>
            <div class="pprice">
                <span class="now tnum">{{ taka($product->price) }}</span>
                @if ($product->compare_price && $product->compare_price > $product->price)
                    <span class="was tnum">{{ taka($product->compare_price) }}</span>
                    <span class="save tnum">Save {{ taka($product->compare_price - $product->price) }}</span>
                @endif
            </div>
            <p class="pdesc">{{ $product->short_desc ?: $product->long_desc }}</p>

            @foreach ($variantGroups as $name => $variants)
                <div class="opt">
                    <div class="lab">{{ $name }}</div>
                    <div class="lensopts">
                        @foreach ($variants as $i => $v)
                            <button type="button" class="{{ $i === 0 ? 'on' : '' }}">{{ $v->value }}@if ($v->price_delta > 0)<small>+{{ taka($v->price_delta) }}</small>@endif</button>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="buyrow" style="margin-bottom:0">
                <div class="qty"><button type="button" aria-label="Decrease" @click="qty=Math.max(1,qty-1)">−</button><span class="q tnum" x-text="qty"></span><button type="button" aria-label="Increase" @click="qty++">+</button></div>
                @php $inWishlist = \App\Support\Wishlist::has($product->id); @endphp
                <form method="POST" action="{{ route('wishlist.toggle', $product) }}" style="margin:0;display:inline;order:3">@csrf<button type="submit" class="wish" aria-label="{{ $inWishlist ? 'Remove from' : 'Add to' }} wishlist" style="{{ $inWishlist ? 'background:var(--sale);color:#fff;border-color:var(--sale)' : '' }}"><svg class="icon" viewBox="0 0 24 24" style="{{ $inWishlist ? 'fill:#fff;stroke:#fff' : '' }}"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"></path></svg></button></form>
            </div>
            <div class="buyrow" style="margin-top:12px">
                <form method="POST" action="{{ route('cart.add') }}" style="flex:1;min-width:180px;margin:0">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="qty" :value="qty">
                    <button type="submit" class="btn btn-ghost" style="width:100%" {{ $inStock ? '' : 'disabled' }}>
                        <svg class="icon sm" viewBox="0 0 24 24"><path d="M6 6h15l-1.5 9h-12z"></path><path d="M6 6L5 3H2"></path><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle></svg>
                        Add to Cart
                    </button>
                </form>
                <form method="POST" action="{{ route('checkout.buy-now') }}" style="flex:1;min-width:180px;margin:0">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="qty" :value="qty">
                    <button type="submit" class="btn btn-primary" style="width:100%" {{ $inStock ? '' : 'disabled' }}>
                        <svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M13 2L4 14h7l-1 8 9-12h-7z"></path></svg>
                        Buy Now — <span x-text="money(price*qty)"></span>
                    </button>
                </form>
            </div>

            <div class="assure">
                <div><svg class="icon sm" viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path><path d="M9.5 12l1.8 1.8L15 10"></path></svg>100% Authentic + Warranty</div>
                <div><svg class="icon sm" viewBox="0 0 24 24"><path d="M3 7h11v8H3z"></path><path d="M14 10h4l3 3v2h-7z"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle></svg>Free Dhaka delivery over ৳2,000</div>
                <div><svg class="icon sm" viewBox="0 0 24 24"><path d="M3 10a9 9 0 0 1 15-4l3 3"></path><path d="M21 4v5h-5"></path><path d="M21 14a9 9 0 0 1-15 4l-3-3"></path><path d="M3 20v-5h5"></path></svg>7-day easy returns</div>
                <div><svg class="icon sm" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path></svg>bKash · Nagad · COD</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'desc' }">
        <div class="tabs">
            <button :class="tab==='desc' && 'on'" @click="tab='desc'">Description</button>
            <button :class="tab==='spec' && 'on'" @click="tab='spec'">Specifications</button>
            <button :class="tab==='rev' && 'on'" @click="tab='rev'">Reviews ({{ $product->approvedReviews->count() }})</button>
        </div>
        <div class="tabpanel" x-show="tab==='desc'">
            <p>{{ $product->long_desc ?: $product->short_desc ?: 'No description available.' }}</p>
        </div>
        <div class="spec" x-show="tab==='spec'" style="display:none">
            @if ($product->brand)<div class="k">Brand</div><div class="v">{{ $product->brand }}</div>@endif
            @if ($product->sku)<div class="k">SKU</div><div class="v">{{ $product->sku }}</div>@endif
            <div class="k">Category</div><div class="v">{{ $product->category?->name ?? '—' }}</div>
            <div class="k">Availability</div><div class="v">{{ $inStock ? $product->stock_qty.' in stock' : 'Out of stock' }}</div>
            <div class="k">UV Protection</div><div class="v">100% UVA/UVB</div>
            <div class="k">Warranty</div><div class="v">1 Year Brand Warranty</div>
        </div>
        <div class="tabpanel" x-show="tab==='rev'" style="display:none;max-width:720px">
            @if (session('status') && str_contains(session('status'), 'review'))
                <div style="background:#f0fdf4;border-left:3px solid #16a34a;border-radius:10px;padding:11px 14px;margin-bottom:18px;color:#166534;font-size:13px">{{ session('status') }}</div>
            @endif

            @forelse ($product->approvedReviews as $review)
                <div class="rev">
                    <div class="top">
                        <span class="who">{{ $review->customer?->name ?? $review->author_name ?? 'Verified Buyer' }}</span>
                        <span class="stars">@for ($i=0;$i<$review->rating;$i++)<svg class="icon" viewBox="0 0 24 24"><path d="M12 3l2.6 5.5 6 .8-4.3 4.2 1 6-5.3-2.9-5.3 2.9 1-6L3.4 9.3l6-.8z"></path></svg>@endfor</span>
                    </div>
                    @if ($review->title)<strong>{{ $review->title }}</strong><br>@endif
                    {{ $review->comment }}
                </div>
            @empty
                <p class="sec-sub">No reviews yet. Be the first to review this product.</p>
            @endforelse

            {{-- Write a review --}}
            <div style="margin-top:28px;padding-top:24px;border-top:1px solid var(--line)">
                <h3 style="font-size:18px;margin:0 0 14px">Write a review</h3>
                @auth('web')
                    @if ($errors->any())
                        <div style="background:#fef2f2;border-left:3px solid var(--sale);border-radius:10px;padding:11px 14px;margin-bottom:14px;font-size:13px;color:var(--sale)">
                            <ul style="margin:0;padding-left:18px">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('product.review.store', $product) }}" x-data="{ rating: {{ old('rating', 5) }} }">
                        @csrf
                        <div style="margin-bottom:12px">
                            <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Your rating</label>
                            <div style="display:inline-flex;gap:4px;cursor:pointer">
                                <template x-for="n in 5" :key="n">
                                    <svg @click="rating=n" class="icon" viewBox="0 0 24 24" style="cursor:pointer;width:26px;height:26px" :style="`stroke:${n<=rating?'var(--sale)':'var(--muted)'};fill:${n<=rating?'var(--sale)':'none'}`"><path d="M12 3l2.6 5.5 6 .8-4.3 4.2 1 6-5.3-2.9-5.3 2.9 1-6L3.4 9.3l6-.8z"></path></svg>
                                </template>
                            </div>
                            <input type="hidden" name="rating" x-model="rating">
                        </div>
                        <div style="margin-bottom:12px"><label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Title (optional)</label><input name="title" value="{{ old('title') }}" maxlength="120" placeholder="Short summary" style="width:100%;border:1px solid var(--line);border-radius:10px;padding:10px 12px;font-family:var(--ui)"></div>
                        <div style="margin-bottom:14px"><label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Your review</label><textarea name="comment" required maxlength="2000" style="width:100%;min-height:100px;border:1px solid var(--line);border-radius:10px;padding:10px 12px;font-family:var(--ui)" placeholder="Share your experience with this product">{{ old('comment') }}</textarea></div>
                        <button type="submit" class="btn btn-primary">Submit review</button>
                    </form>
                @else
                    <p class="sec-sub"><a href="{{ route('customer.login') }}">Sign in</a> to write a review.</p>
                @endauth
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if ($related->isNotEmpty())
    <section class="sec rel"><div class="wrap" style="padding:0">
        <h2>You May Also Like</h2>
        <div class="prod-grid">
            @foreach ($related as $rel)
                @include('storefront.partials.product-card', ['product' => $rel])
            @endforeach
        </div>
    </div></section>
    @endif
</div>

@push('scripts')
<script>
function productPage(price, images) {
    return {
        price, images, current: 0, qty: 1,
        zooming: false, zoomX: 50, zoomY: 50, showVideo: false,
        money(n) { return '৳' + Number(n).toLocaleString('en-IN'); },
        zoomMove(e) {
            const rect = e.currentTarget.getBoundingClientRect();
            this.zoomX = ((e.clientX - rect.left) / rect.width) * 100;
            this.zoomY = ((e.clientY - rect.top) / rect.height) * 100;
        },
    };
}
</script>
@endpush
@endsection

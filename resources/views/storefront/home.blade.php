@extends('layouts.storefront')

@section('title', 'Your Trusted Vision Partner')

@php
    use App\Models\SiteSetting;
    $s = SiteSetting::allValues();
    $heroImg = ($s['hero_image'] ?? null) ? asset('storage/'.$s['hero_image']) : asset('images/storefront/hero-photo-crop.png');
    $heroHeadline = $s['hero_headline'] ?? 'See the World in a Better Way';
    $heroSub = $s['hero_subheadline'] ?? 'Premium eyewear for your style, comfort and clearer vision. Discover eyeglasses, sunglasses, contact lenses and more.';
    $heroCtaText = $s['hero_cta_text'] ?? 'Shop Now';
    $heroCtaLink = $s['hero_cta_link'] ?? route('products.index');
@endphp

@section('content')
    {{-- Hero --}}
    <section class="hero" id="top">
        <div class="wrap">
            <div>
                <div class="eyebrow">Clearer Vision &middot; Brighter Tomorrow</div>
                <h1>{{ $heroHeadline }}</h1>
                <p class="lead">{{ $heroSub }}</p>
                <div class="hero-trust">
                    <span><svg class="icon sm" viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path><path d="M9.5 12l1.8 1.8L15 10"></path></svg>100% Authentic Products</span>
                    <span><svg class="icon sm" viewBox="0 0 24 24"><path d="M3 7h11v8H3z"></path><path d="M14 10h4l3 3v2h-7z"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle></svg>Free Shipping Over ৳2,000</span>
                    <span><svg class="icon sm" viewBox="0 0 24 24"><path d="M3 10a9 9 0 0 1 15-4l3 3"></path><path d="M21 4v5h-5"></path><path d="M21 14a9 9 0 0 1-15 4l-3-3"></path><path d="M3 20v-5h5"></path></svg>Easy Returns</span>
                </div>
                <div class="hero-cta">
                    <a href="{{ $heroCtaLink }}" class="btn btn-primary">{{ $heroCtaText }}<svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg></a>
                    <a href="{{ route('products.index') }}" class="btn btn-ghost">Browse Collection</a>
                </div>
            </div>
            <div class="hero-photo"><img src="{{ $heroImg }}" alt="{{ $heroHeadline }}"></div>
        </div>
    </section>

    {{-- Shop by Category --}}
    <section class="sec" id="categories">
        <div class="wrap">
            <div class="sec-head">
                <div>
                    <div class="eyebrow">Browse</div>
                    <h2>Shop by Category</h2>
                    <div class="sec-sub">Find the perfect pair for every need and every face.</div>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">View All</a>
            </div>
            <div class="cat-grid">
                @forelse ($categories as $cat)
                    <a href="{{ route('category.show', $cat) }}" class="cat-tile">
                        <span class="cat-chip">
                            @if ($cat->icon && ! str_contains($cat->icon, '<'))<span style="font-size:20px">{{ $cat->icon }}</span>
                            @else<svg class="icon" viewBox="0 0 24 24"><rect x="2" y="9" width="8" height="6" rx="3"></rect><rect x="14" y="9" width="8" height="6" rx="3"></rect><path d="M10 11c1-1 3-1 4 0"></path></svg>@endif
                        </span>
                        <h3>{{ $cat->name }}</h3><p>{{ \Illuminate\Support\Str::limit($cat->description, 46) ?: 'Explore the collection.' }}</p>
                        <button class="cat-arrow" aria-label="Shop {{ $cat->name }}"><svg class="icon sm" viewBox="0 0 24 24"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg></button>
                    </a>
                @empty
                    <p class="sec-sub">Categories will appear here once added in the admin panel.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Flash Sale --}}
    @if ($flash->isNotEmpty())
    <section class="sec" id="offers" style="padding-top:12px">
        <div class="wrap">
            <div class="flash">
                <div>
                    <div class="bolt"><svg class="icon lg" viewBox="0 0 24 24"><path d="M13 2L4 14h7l-1 8 9-12h-7z"></path></svg></div>
                    <h2>Flash Sale</h2>
                    <p>Limited-time prices on bestselling frames and lenses. Grab yours before they're gone.</p>
                    <a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="btn btn-primary">Shop the Sale</a>
                </div>
                <div class="flash-prod">
                    @foreach ($flash as $p)
                        <div class="mini">
                            @if ($p->discount_percent)<span class="off">-{{ $p->discount_percent }}%</span>@endif
                            <a class="ph" href="{{ route('product.show', $p) }}">
                                @if ($p->primaryImage)<img class="pimg" src="{{ asset('storage/'.$p->primaryImage->image_path) }}" alt="{{ $p->name }}">
                                @else<svg class="glass" viewBox="0 0 200 90" style="width:80%"><circle cx="100" cy="45" r="28"></circle></svg>@endif
                            </a>
                            <h4>{{ $p->name }}</h4>
                            <div class="price"><span class="now tnum">{{ taka($p->price) }}</span>@if ($p->compare_price)<span class="was tnum">{{ taka($p->compare_price) }}</span>@endif</div>
                            <form method="POST" action="{{ route('cart.add') }}">@csrf<input type="hidden" name="product_id" value="{{ $p->id }}"><button class="add" type="submit" aria-label="Add {{ $p->name }}"><svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg></button></form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Best Sellers --}}
    <section class="sec" id="bestsellers" style="padding-top:12px">
        <div class="wrap">
            <div class="sec-head">
                <div>
                    <div class="eyebrow">Loved by customers</div>
                    <h2>Best Selling Products</h2>
                    <div class="sec-sub">The frames and lenses our customers reach for again and again.</div>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">View All</a>
            </div>
            <div class="prod-grid">
                @foreach ($bestSellers as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>

    {{-- Promo banners --}}
    <section class="sec" style="padding-top:12px">
        <div class="wrap">
            <div class="promos">
                <div class="promo a">
                    <div class="deco"></div>
                    <svg class="glass" viewBox="0 0 200 90"><path d="M2 20h196"></path><rect x="12" y="20" width="72" height="50" rx="14"></rect><rect x="116" y="20" width="72" height="50" rx="14"></rect><path d="M84 34h32"></path></svg>
                    <div class="eyebrow">Stylish Sunglasses</div>
                    <h3>For Every Adventure</h3>
                    <p>UV400 protection meets head-turning design.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm" style="background:#fff;border-color:#fff;color:var(--blue)">Explore Sunglasses</a>
                </div>
                <div class="promo b">
                    <div class="deco"></div>
                    <svg class="glass" viewBox="0 0 200 90"><rect x="10" y="22" width="78" height="46" rx="10"></rect><rect x="112" y="22" width="78" height="46" rx="10"></rect><path d="M88 40h24"></path></svg>
                    <div class="eyebrow">Stay Productive, Stay Comfortable</div>
                    <h3>Blue Light Glasses</h3>
                    <p>Reduce digital eye strain through long screen hours.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm" style="background:#fff;border-color:#fff;color:var(--navy)">Shop Computer Glasses</a>
                </div>
            </div>
        </div>
    </section>

    {{-- All Products --}}
    <section class="sec" id="allproducts" style="padding-top:12px">
        <div class="wrap">
            <div class="sec-head">
                <div>
                    <div class="eyebrow">Explore the collection</div>
                    <h2>All Products</h2>
                    <div class="sec-sub">Browse the full range — filter by what you're looking for.</div>
                </div>
            </div>
            <div class="filters" style="margin-bottom:26px">
                <a href="{{ route('products.index') }}" class="fpill on">All</a>
                @foreach ($categories as $cat)
                    <a href="{{ route('category.show', $cat) }}" class="fpill">{{ $cat->name }}</a>
                @endforeach
            </div>
            <div class="prod-grid">
                @foreach ($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>

    {{-- Newsletter --}}
    <section class="sec" style="padding-top:12px">
        <div class="wrap">
            <div class="news">
                <span class="nic"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M4 7l8 6 8-6"></path></svg></span>
                <div class="news-col">
                    <h3>Get 10% off your first order</h3>
                    <p>Join the Vision Studio list for new arrivals, exclusive offers and eye-care tips.</p>
                    <label class="consent"><input type="checkbox"> I agree to receive marketing emails from Vision Studio.</label>
                </div>
                @if (session('status') && str_contains(strtolower(session('status')), 'subscrib'))
                    <div style="background:#f0fdf4;border-left:3px solid #16a34a;border-radius:10px;padding:11px 14px;margin-bottom:12px;color:#166534;font-size:13px">{{ session('status') }}</div>
                @endif
                <form class="news-form" method="POST" action="{{ route('newsletter.subscribe') }}">
                    @csrf
                    <input type="email" name="email" aria-label="Email address" placeholder="your@email.com" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
                @error('email')<p style="color:var(--sale);font-size:12px;margin-top:6px">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    {{-- Top Brands --}}
    @if ($brands->isNotEmpty())
    <section class="sec" id="brands" style="padding-top:12px">
        <div class="wrap">
            <div class="sec-head" style="justify-content:center;text-align:center;margin-bottom:24px">
                <div><div class="eyebrow">Authorised retailer</div><h2>Shop by Top Brands</h2></div>
            </div>
            <div class="brands">
                @foreach ($brands as $brand)
                    <a href="{{ route('products.index', ['brand' => $brand]) }}" class="brand">{{ $brand }}</a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection

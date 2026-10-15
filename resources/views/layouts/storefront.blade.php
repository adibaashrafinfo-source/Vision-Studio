@php
    use App\Models\SiteSetting;
    use App\Models\Category;
    use App\Support\Cart;
    $s = SiteSetting::allValues();
    $logo = ($s['logo_path'] ?? null) ? asset('storage/'.$s['logo_path']) : asset('images/storefront/3cb21dabe62909e3717bede6df8fb59a.jpg');
    $navCats = Category::where('status', 'active')->whereNull('parent_id')->orderBy('position')->limit(6)->get();
    $cartCount = Cart::count();
    $customer = auth('web')->user();
    $appName = $s['store_name'] ?? config('app.name');
    // Theme overrides from Appearance settings.
    $themeVars = collect([
        '--navy' => $s['theme_navy'] ?? null, '--blue' => $s['theme_blue'] ?? null,
        '--blue-bright' => $s['theme_blue_bright'] ?? null, '--sale' => $s['theme_sale'] ?? null,
        '--paper' => $s['theme_paper'] ?? null,
    ])->filter()->map(fn ($v, $k) => "$k:$v")->implode(';');
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $metaTitle = (View::getSection('title') ?? 'Premium Eyewear').' · '.$appName;
        $metaDesc = trim(View::yieldContent('metaDescription') ?: ($s['site_description'] ?? 'Premium eyewear for Bangladesh — authentic Ray-Ban, Oakley, prescription glasses, sunglasses and contact lenses. Nationwide delivery, 7-day returns.'));
        $metaImage = ($s['hero_image'] ?? null) ? asset('storage/'.$s['hero_image']) : asset('images/storefront/85172a1c7dc613e91dd4e7982525e409.png');
        $canonical = url()->current();
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="@yield('ogType', 'website')">
    <meta property="og:site_name" content="{{ $appName }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('storefront/app.css') }}">
    @stack('styles')
    @if ($themeVars)<style>:root{ {{ $themeVars }} }</style>@endif
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
</head>
<body>
<div class="vs">
    @if (session('status'))
        <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)" style="position:fixed;top:16px;right:16px;z-index:100;background:var(--navy);color:#fff;padding:13px 18px;border-radius:12px;box-shadow:0 12px 30px -12px rgba(10,26,66,.6);font-size:14px;font-family:var(--ui);display:flex;gap:12px;align-items:center">
            <span>{{ session('status') }}</span>
            <button @click="show=false" style="background:none;border:0;color:#fff;cursor:pointer;font-size:16px">&times;</button>
        </div>
    @endif

    {{-- Utility bar --}}
    <div class="util">
        <div class="wrap">
            <div class="util-l">
                <span><svg class="icon sm" viewBox="0 0 24 24"><path d="M3 7h11v8H3z"></path><path d="M14 10h4l3 3v2h-7z"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle></svg>{{ $s['announcement_text'] ?? 'Free Shipping on Orders Over ৳2,000' }}</span>
                <span><svg class="icon sm" viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path><path d="M9.5 12l1.8 1.8L15 10"></path></svg>100% Authentic Products</span>
                <span><svg class="icon sm" viewBox="0 0 24 24"><path d="M3 10a9 9 0 0 1 15-4l3 3"></path><path d="M21 4v5h-5"></path><path d="M21 14a9 9 0 0 1-15 4l-3-3"></path><path d="M3 20v-5h5"></path></svg>Easy Returns</span>
            </div>
            <div class="util-r">
                <a href="{{ route('account') }}">Track Order</a>
                <a href="{{ route('stores') }}">Store Locator</a>
                <a href="{{ route('help') }}">Need Help?</a>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <div class="nav" x-data="{ mobileOpen:false }">
        <div class="wrap">
            <button type="button" class="hamburger" @click="mobileOpen=!mobileOpen" :aria-expanded="mobileOpen.toString()" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
            <a href="{{ route('home') }}" class="logo" aria-label="{{ $appName }} home"><img src="{{ $logo }}" alt="{{ $appName }}"></a>
            <form class="search" action="{{ route('products.index') }}" method="GET">
                <svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4-4"></path></svg>
                <input type="search" name="q" value="{{ request('q') }}" aria-label="Search products" placeholder="Search eyeglasses, sunglasses, lenses…">
                <button type="submit">Search</button>
            </form>
            <div class="nav-actions">
                <a href="{{ $customer ? route('account') : route('customer.login') }}" class="iconbtn" aria-label="Account">
                    <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"></circle><path d="M4 20c0-4 4-6 8-6s8 2 8 6"></path></svg>
                    <span>{{ $customer ? 'Account' : 'Sign in' }}</span>
                </a>
                @php $wishlistCount = \App\Support\Wishlist::count(); @endphp
                <a href="{{ route('wishlist.index') }}" class="iconbtn" aria-label="Wishlist">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"></path></svg>
                    <span>Wishlist</span>
                    @if ($wishlistCount)<span class="badge">{{ $wishlistCount }}</span>@endif
                </a>
                <a href="{{ route('cart.index') }}" class="iconbtn" aria-label="Cart">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M6 6h15l-1.5 9h-12z"></path><path d="M6 6L5 3H2"></path><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle></svg>
                    <span>Cart</span>
                    @if ($cartCount)<span class="badge">{{ $cartCount }}</span>@endif
                </a>
            </div>
        </div>

        {{-- Mobile slide-down menu --}}
        <div class="mobile-menu" x-show="mobileOpen" x-transition x-cloak @click.outside="mobileOpen=false">
            <form class="mobile-search" action="{{ route('products.index') }}" method="GET">
                <svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4-4"></path></svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products…">
            </form>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            @foreach ($navCats as $cat)
                <a href="{{ route('category.show', $cat) }}">{{ $cat->name }}</a>
            @endforeach
            <a href="{{ route('products.index') }}">All Products</a>
            <a href="{{ route('products.index', ['sort' => 'price_low']) }}" style="color:var(--sale);font-weight:700">Special Sale</a>
            <div class="mobile-menu-divider"></div>
            <a href="{{ $customer ? route('account') : route('customer.login') }}">{{ $customer ? 'My Account' : 'Sign In' }}</a>
            <a href="{{ route('wishlist.index') }}">Wishlist @if ($wishlistCount)<span class="badge" style="position:static;margin-left:6px">{{ $wishlistCount }}</span>@endif</a>
            <a href="{{ route('cart.index') }}">Cart @if ($cartCount)<span class="badge" style="position:static;margin-left:6px">{{ $cartCount }}</span>@endif</a>
        </div>
    </div>
    <div class="catnav">
        <div class="wrap">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            @foreach ($navCats as $cat)
                <a href="{{ route('category.show', $cat) }}" class="{{ request()->routeIs('category.show') && request()->route('category')?->id === $cat->id ? 'active' : '' }}">{{ $cat->name }}</a>
            @endforeach
            <a href="{{ route('products.index') }}">All Products</a>
            <span class="spacer"></span>
            <a href="{{ route('products.index', ['sort' => 'price_low']) }}" class="pill-sale"><svg class="icon sm" viewBox="0 0 24 24" style="stroke:#fff"><path d="M13 2L4 14h7l-1 8 9-12h-7z"></path></svg>Special Sale</a>
        </div>
    </div>

    @yield('content')

    {{-- Footer --}}
    <footer class="footer">
        <div class="wrap">
            <div class="foot-grid">
                <div class="foot-brand">
                    <span class="foot-logo"><img src="{{ $logo }}" alt="{{ $appName }}"></span>
                    <p>{{ $s['footer_about'] ?? 'Your Trusted Vision Partner — authentic eyewear, prescription lenses and expert care, delivered across Bangladesh.' }}</p>
                    <div class="socials">
                        <a href="{{ $s['social_facebook'] ?? '#' }}" aria-label="Facebook"><svg class="icon" viewBox="0 0 24 24"><path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H7v4h3v6h4v-6h3l1-4h-4V8a1 1 0 0 1 1-1z"></path></svg></a>
                        <a href="{{ $s['social_instagram'] ?? '#' }}" aria-label="Instagram"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1"></circle></svg></a>
                        <a href="{{ $s['social_youtube'] ?? '#' }}" aria-label="YouTube"><svg class="icon" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="4"></rect><path d="M10 9l5 3-5 3z"></path></svg></a>
                        <a href="{{ $s['social_tiktok'] ?? '#' }}" aria-label="TikTok"><svg class="icon" viewBox="0 0 24 24"><path d="M4 20l1.5-4A8 8 0 1 1 9 19z"></path><path d="M9 10c0 3 2 5 5 5"></path></svg></a>
                    </div>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <ul class="foot-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('products.index') }}">Shop All</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'price_low']) }}">Offers &amp; Deals</a></li>
                        <li><a href="{{ route('account') }}">My Account</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Customer Service</h4>
                    <ul class="foot-links">
                        <li><a href="{{ route('account') }}">Track Your Order</a></li>
                        <li><a href="{{ route('shipping') }}">Shipping &amp; Delivery</a></li>
                        <li><a href="{{ route('returns') }}">Returns &amp; Refunds</a></li>
                        <li><a href="{{ route('faq') }}">FAQ</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Contact Info</h4>
                    <div class="foot-contact">
                        <div><svg class="icon sm" viewBox="0 0 24 24"><path d="M4 5c0 9 6 15 15 15l1-4-5-2-2 2a12 12 0 0 1-5-5l2-2-2-5z"></path></svg><span>{{ $s['footer_phone'] ?? '+880 1719-686459' }}</span></div>
                        <div><svg class="icon sm" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M4 7l8 6 8-6"></path></svg><span>{{ $s['footer_email'] ?? 'hello@visionstudio.com.bd' }}</span></div>
                        <div><svg class="icon sm" viewBox="0 0 24 24"><path d="M12 21s-7-6-7-11a7 7 0 0 1 14 0c0 5-7 11-7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg><span>{{ $s['footer_address'] ?? 'Showroom: Level 3, Bashundhara City, Panthapath, Dhaka 1205' }}</span></div>
                    </div>
                </div>
            </div>
            <div class="foot-bottom">
                <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
                <div class="pays">
                    <span class="pay visa">VISA</span>
                    <span class="pay mc">MasterCard</span>
                    <span class="pay amex">AMEX</span>
                    <span class="pay bkash">bKash</span>
                    <span class="pay nagad">Nagad</span>
                    <span class="pay">Cash on Delivery</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- Quick View modal --}}
    <div x-data="{ show:false, p:null }" x-init="window.addEventListener('quick-view', e => { p = e.detail; show = true })" x-show="show" x-cloak
         class="qv-overlay">
        <div x-show="show" x-transition.opacity @click="show=false" style="position:absolute;inset:0;background:rgba(10,18,40,.6)"></div>
        <div x-show="show" x-transition.scale.95 class="qv-modal" @click.outside="show=false" style="position:relative" x-cloak>
            <button type="button" class="qv-close" @click="show=false" aria-label="Close">&times;</button>
            <template x-if="p">
                <div class="qv-body">
                    <div class="qv-img">
                        <template x-if="p.image"><img :src="p.image" :alt="p.name"></template>
                        <template x-if="!p.image"><svg class="glass" viewBox="0 0 200 90" style="width:60%"><circle cx="100" cy="45" r="30"></circle><circle cx="100" cy="45" r="8"></circle></svg></template>
                    </div>
                    <div class="qv-info">
                        <span class="cat" x-text="p.category"></span>
                        <h3 x-text="p.name"></h3>
                        <div class="rating"><svg class="icon" viewBox="0 0 24 24"><path d="M12 3l2.6 5.5 6 .8-4.3 4.2 1 6-5.3-2.9-5.3 2.9 1-6L3.4 9.3l6-.8z"></path></svg><b x-text="p.rating"></b><span>(<span x-text="p.reviewsCount"></span>)</span></div>
                        <div class="qv-price">
                            <span class="now tnum" x-text="'৳'+Number(p.price).toLocaleString('en-IN')"></span>
                            <template x-if="p.comparePrice"><span class="was tnum" x-text="'৳'+Number(p.comparePrice).toLocaleString('en-IN')"></span></template>
                        </div>
                        <template x-if="!p.inStock"><p style="color:var(--sale);font-weight:600;font-size:13px">Out of stock</p></template>
                        <form method="POST" :action="`{{ url('/cart/add') }}`" style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="product_id" :value="p.productId">
                            <button type="submit" class="btn btn-primary" :disabled="!p.inStock">Add to Cart</button>
                            <a :href="p.url" class="btn btn-ghost">View Full Details</a>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- WhatsApp floating button --}}
    @php $waNumber = preg_replace('/\D/', '', $s['whatsapp_number'] ?? $s['footer_phone'] ?? '+8801719686459'); @endphp
    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="wa-float" aria-label="Chat on WhatsApp">
        <span class="wa-ping"></span>
        <svg viewBox="0 0 32 32" width="30" height="30" fill="#fff"><path d="M16.02 3C9.4 3 4 8.4 4 15.02c0 2.6.8 5 2.2 7L4.5 29l7.2-1.9c1.9 1 4.1 1.6 6.3 1.6 6.6 0 12-5.4 12-12.02C30 8.4 24.6 3 16.02 3zm0 22c-1.9 0-3.7-.5-5.3-1.4l-.4-.2-4.3 1.1 1.1-4.2-.2-.4c-1-1.6-1.5-3.5-1.5-5.4 0-5.5 4.5-10 10-10s10 4.5 10 10-4.4 10.5-9.4 10.5zm5.6-7.5c-.3-.1-1.8-.9-2.1-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-1 1.2-.2.2-.4.2-.7.1-.3-.1-1.4-.5-2.6-1.6-1-.9-1.6-2-1.8-2.3-.2-.3 0-.5.1-.6.1-.1.3-.4.4-.5.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5 0-.1-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1.1 2.8 1.2 3c.1.2 2.1 3.2 5.1 4.4.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.2-.3-.2-.6-.4z"></path></svg>
    </a>
</div>
@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var targets = document.querySelectorAll('[data-animate]');
    if (!('IntersectionObserver' in window) || !targets.length) {
        targets.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    targets.forEach(function (el) { observer.observe(el); });
});
</script>
</body>
</html>

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
    <title>@yield('title', 'Premium Eyewear')  · {{ $appName }}</title>
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
                <a href="#stores">Store Locator</a>
                <a href="#help">Need Help?</a>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <div class="nav">
        <div class="wrap">
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
                <a href="{{ route('account') }}" class="iconbtn" aria-label="Wishlist">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"></path></svg>
                    <span>Wishlist</span>
                </a>
                <a href="{{ route('cart.index') }}" class="iconbtn" aria-label="Cart">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M6 6h15l-1.5 9h-12z"></path><path d="M6 6L5 3H2"></path><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle></svg>
                    <span>Cart</span>
                    @if ($cartCount)<span class="badge">{{ $cartCount }}</span>@endif
                </a>
            </div>
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
                        <li><a href="#shipping">Shipping &amp; Delivery</a></li>
                        <li><a href="#returns">Returns &amp; Refunds</a></li>
                        <li><a href="#faq">FAQ</a></li>
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
</div>
@stack('scripts')
</body>
</html>

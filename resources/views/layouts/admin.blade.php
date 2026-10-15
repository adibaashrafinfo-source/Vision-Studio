@php
    use App\Models\SiteSetting;
    $admin = auth('admin')->user();
    $initials = collect(explode(' ', trim($admin?->name ?? 'Admin')))
        ->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $logo = SiteSetting::get('logo_path');
    $logoUrl = $logo ? asset('storage/'.$logo) : asset('admin-assets/logo.jpg');
    $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
    $pendingReviews = \App\Models\Review::where('status', 'pending')->count();
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin-assets/admin.css') }}">
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    @stack('head')
</head>
<body>
<div class="vs">
    <div class="adm">
        <aside class="adm-side">
            <div class="adm-logo">
                <span class="lmk"><img src="{{ $logoUrl }}" alt="{{ config('app.name') }}"></span>
                <div class="lt"><b>{{ config('app.name') }}</b><span>E-Commerce Admin</span></div>
            </div>
            <nav class="adm-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect></svg>Dashboard</a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"></path><path d="M14 2v6h6"></path><path d="M9 13h7"></path><path d="M9 17h7"></path></svg>Orders @if ($pendingOrders)<span class="nbadge">{{ $pendingOrders }}</span>@endif</a>
                <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"></path><path d="M3 7v10l9 4 9-4V7"></path><path d="M12 11v10"></path></svg>Products</a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="8" rx="2"></rect><rect x="13" y="3" width="8" height="8" rx="2"></rect><rect x="3" y="13" width="8" height="8" rx="2"></rect><rect x="13" y="13" width="8" height="8" rx="2"></rect></svg>Categories</a>
                <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"></circle><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"></path><circle cx="18" cy="8" r="2.8" opacity=".55"></circle><path d="M16 14.3c2.6.5 4.5 2.4 5 5.7" opacity=".55"></path></svg>Customers</a>
                <a href="{{ route('admin.website.edit') }}" class="{{ request()->routeIs('admin.website.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18"></path><path d="M12 3c2.8 2.6 4 5.6 4 9s-1.2 6.4-4 9c-2.8-2.6-4-5.6-4-9s1.2-6.4 4-9z"></path></svg>Website</a>
                <a href="{{ route('admin.marketing.index') }}" class="{{ request()->routeIs('admin.marketing.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><path d="M3 11v2a2 2 0 0 0 2 2h2l5 4V5l-5 4H5a2 2 0 0 0-2 2z"></path><path d="M17 8a5 5 0 0 1 0 8" opacity=".6"></path></svg>Marketing</a>
                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><path d="M4 19V9"></path><path d="M11 19V5"></path><path d="M18 19v-7"></path><path d="M3 19h18"></path></svg>Reports</a>
                <a href="{{ route('admin.blog.index') }}" class="{{ request()->routeIs('admin.blog.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><path d="M5 4h14v16H5z"></path><path d="M8 9h8"></path><path d="M8 13h8"></path><path d="M8 17h5"></path></svg>Blog</a>
                <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><path d="M12 3l2.6 5.5 6 .8-4.4 4.1 1.1 6-5.3-2.9-5.3 2.9 1.1-6L3.4 9.3l6-.8z"></path></svg>Reviews @if ($pendingReviews)<span class="nbadge">{{ $pendingReviews }}</span>@endif</a>
                <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4z"></path><path d="M10 7v10" stroke-dasharray="2.4 2.4"></path></svg>Coupons</a>
                <a href="{{ route('admin.appearance.edit') }}" class="{{ request()->routeIs('admin.appearance.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><path d="M12 2l2.3 4.8 5.3.7-3.9 3.7.9 5.3-4.6-2.5-4.6 2.5.9-5.3-3.9-3.7 5.3-.7z"></path></svg>Appearance</a>
                <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'on' : '' }}"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3.2"></circle><path d="M19.4 13a7.6 7.6 0 0 0 0-2l2-1.5-2-3.4-2.3.9a7.5 7.5 0 0 0-1.7-1L15 3h-4l-.4 2.4a7.5 7.5 0 0 0-1.7 1l-2.3-.9-2 3.4L6.6 11a7.6 7.6 0 0 0 0 2l-2 1.5 2 3.4 2.3-.9c.5.4 1.1.8 1.7 1L10 21h4l.4-2.4c.6-.2 1.2-.6 1.7-1l2.3.9 2-3.4z"></path></svg>Settings</a>
                <div class="nav-sec">Sales Channels</div>
                <a href="{{ route('home') }}" target="_blank"><svg class="icon" viewBox="0 0 24 24"><path d="M3 9l1-5h16l1 5"></path><path d="M4 9v11h16V9"></path><path d="M9 20v-6h6v6"></path></svg>Online Store<span class="ch-dot"></span></a>
            </nav>
            <div class="adm-foot">
                <div class="adm-av">{{ $initials }}</div>
                <div><div class="nm">{{ $admin?->name }}</div><div class="rl">{{ ucwords(str_replace('_', ' ', $admin?->role ?? '')) }}</div></div>
                <form method="POST" action="{{ route('admin.logout') }}" class="lo" style="margin:0">
                    @csrf
                    <button type="submit" title="Sign out" style="background:none;border:0;padding:0;color:inherit;cursor:pointer;display:inline-flex">
                        <svg class="icon sm" viewBox="0 0 24 24"><path d="M15 12H4"></path><path d="M8 8l-4 4 4 4"></path><path d="M14 4h5v16h-5"></path></svg>
                    </button>
                </form>
            </div>
        </aside>

        <div class="adm-main">
            <div class="adm-top">
                <form class="adm-search" method="GET" action="{{ $searchAction ?? '' }}">
                    <svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4-4"></path></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products, orders, customers…">
                </form>
                <div class="adm-top-r">
                    <a href="{{ route('admin.orders.index') }}" class="adm-ic"><svg class="icon" viewBox="0 0 24 24"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.7 21a2 2 0 0 1-3.4 0"></path></svg>@if ($pendingOrders)<span class="dot"></span>@endif</a>
                    <a href="{{ route('admin.settings.edit') }}" class="adm-ic"><svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M9.2 9a3 3 0 0 1 5.6 1c0 2-3 2.2-3 4"></path><circle cx="12" cy="17.5" r=".6"></circle></svg></a>
                    <div class="adm-who"><div class="av">{{ $initials }}</div><div><div class="nm">{{ $admin?->name }}</div><div class="rl">{{ ucwords(str_replace('_', ' ', $admin?->role ?? '')) }}</div></div></div>
                </div>
            </div>

            <div class="adm-body">
                @if (session('status'))
                    <div class="flash flash-ok" x-data="{show:true}" x-show="show">
                        <span>{{ session('status') }}</span>
                        <button type="button" @click="show=false" aria-label="Dismiss">&times;</button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="flash flash-err">
                        <ul style="margin:0;padding-left:18px">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>

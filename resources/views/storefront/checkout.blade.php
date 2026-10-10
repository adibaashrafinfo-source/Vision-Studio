<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('storefront/app.css') }}">
    <link rel="stylesheet" href="{{ asset('storefront/checkout.css') }}">
    @php
        use App\Models\SiteSetting;
        $ss = SiteSetting::allValues();
        $logo = ($ss['logo_path'] ?? null) ? asset('storage/'.$ss['logo_path']) : asset('images/storefront/3cb21dabe62909e3717bede6df8fb59a.jpg');
        $total = $subtotal - $discount + $shipping;
        $themeVars = collect([
            '--navy' => $ss['theme_navy'] ?? null, '--blue' => $ss['theme_blue'] ?? null,
            '--blue-bright' => $ss['theme_blue_bright'] ?? null, '--sale' => $ss['theme_sale'] ?? null,
            '--paper' => $ss['theme_paper'] ?? null,
        ])->filter()->map(fn ($v, $k) => "$k:$v")->implode(';');
        $firstMethod = array_key_first($methods);
    @endphp
    @if ($themeVars)<style>:root{ {{ $themeVars }} }</style>@endif
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
</head>
<body>
<div class="vs" x-data="{ pm: '{{ $firstMethod }}' }">
    <div class="util"><div class="wrap">
        <div class="util-l"><span><svg class="icon sm" viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path><path d="M9.5 12l1.8 1.8L15 10"></path></svg>Secure SSL Checkout</span><span><svg class="icon sm" viewBox="0 0 24 24"><path d="M3 7h11v8H3z"></path><path d="M14 10h4l3 3v2h-7z"></path></svg>Nationwide Delivery</span></div>
        <div class="util-r"><a href="{{ route('cart.index') }}">Back to Cart</a></div>
    </div></div>
    <div class="nav"><div class="wrap">
        <a href="{{ route('home') }}" class="logo" aria-label="{{ config('app.name') }} home"><img src="{{ $logo }}" alt="{{ config('app.name') }}"></a>
        <span class="securetag" style="margin-left:auto;display:inline-flex;align-items:center;gap:7px;font-weight:600;font-size:13px;color:var(--muted)"><svg class="icon sm" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>100% Secure Checkout</span>
    </div></div>

    <div class="wrap">
        <div class="steps">
            <span class="step done"><span class="n">✓</span>Cart</span><span class="bar"></span>
            <span class="step cur"><span class="n">2</span>Delivery &amp; Payment</span><span class="bar"></span>
            <span class="step"><span class="n">3</span>Confirmation</span>
        </div>

        @if ($errors->any())
            <div class="panel" style="border-left:3px solid var(--sale)"><ul style="margin:0;padding-left:18px;color:var(--sale)">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}">
            @csrf
            <div class="co">
                <div>
                    <div class="panel">
                        <h2>Contact Information</h2>
                        <div class="sub">We'll send order updates by SMS and email.</div>
                        <div class="grid2">
                            <div class="field"><label for="fn">Full Name</label><input id="fn" name="name" type="text" value="{{ old('name', $customer?->name) }}" placeholder="Your name" required></div>
                            <div class="field"><label for="ph">Mobile Number</label><input id="ph" name="phone" type="tel" value="{{ old('phone', $customer?->phone) }}" placeholder="+880 1XXX-XXXXXX" required></div>
                        </div>
                        <div class="field"><label for="em">Email Address</label><input id="em" name="email" type="email" value="{{ old('email', $customer?->email) }}" placeholder="your@email.com" required></div>
                    </div>

                    <div class="panel">
                        <h2>Delivery Address</h2>
                        <div class="sub">Where should we send your order?</div>
                        <div class="field"><label for="ad">Street Address</label><input id="ad" name="address_line" type="text" value="{{ old('address_line') }}" placeholder="House / Road / Area" required></div>
                        <div class="field"><label for="ct">City / District</label><input id="ct" name="city" type="text" value="{{ old('city', $customer?->location_city) }}" placeholder="e.g. Dhaka" required></div>
                        <div class="field"><label for="nt">Order Notes (optional)</label><input id="nt" name="notes" type="text" value="{{ old('notes') }}" placeholder="Any delivery instructions"></div>
                    </div>

                    <div class="panel">
                        <h2>Payment Method</h2>
                        <div class="sub">All transactions are encrypted and secure.</div>
                        <input type="hidden" name="payment_method" :value="pm">
                        <div class="pays-list">
                            @foreach ($methods as $key => $m)
                                <button :class="pm==='{{ $key }}' && 'on'" type="button" @click="pm='{{ $key }}'"><span class="rd"></span><span class="logo-chip" style="background:{{ $m['color'] }}">{{ strtoupper($key) }}</span><span class="name">{{ $m['label'] }}<small>{{ $m['sub'] }}</small></span></button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <aside class="summary">
                    <h3>Your Order</h3>
                    @foreach ($items as $it)
                        <div class="oi">
                            <div class="th">
                                @if ($it['image'])<img class="pimg" src="{{ asset('storage/'.$it['image']) }}" alt="{{ $it['name'] }}">
                                @else<svg class="glass" viewBox="0 0 200 90"><circle cx="100" cy="45" r="30"></circle></svg>@endif
                            </div>
                            <div class="nm">{{ $it['name'] }}<small>× {{ $it['qty'] }}</small></div>
                            <div class="pr tnum">{{ taka($it['price'] * $it['qty']) }}</div>
                        </div>
                    @endforeach
                    <div style="height:12px"></div>
                    <div class="srow"><span>Subtotal</span><span class="v tnum">{{ taka($subtotal) }}</span></div>
                    @if ($discount > 0)<div class="srow"><span>Discount</span><span class="v tnum" style="color:var(--sale)">−{{ taka($discount) }}</span></div>@endif
                    <div class="srow"><span>Shipping</span><span class="v tnum">{{ $shipping == 0 ? 'Free' : taka($shipping) }}</span></div>
                    <div class="stot"><span class="l">Total</span><span class="v tnum">{{ taka($total) }}</span></div>
                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:14px">Place Order — {{ taka($total) }}</button>
                    <div class="guar"><svg class="icon sm" viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path><path d="M9.5 12l1.8 1.8L15 10"></path></svg>Encrypted &amp; secure · 7-day returns</div>
                </aside>
            </div>
        </form>
    </div>

    <footer class="footer"><div class="wrap"><div class="foot-bottom"><p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p><div class="pays"><span class="pay visa">VISA</span><span class="pay mc">MasterCard</span><span class="pay amex">AMEX</span><span class="pay bkash">bKash</span><span class="pay nagad">Nagad</span><span class="pay">Cash on Delivery</span></div></div></div></footer>
</div>
</body>
</html>

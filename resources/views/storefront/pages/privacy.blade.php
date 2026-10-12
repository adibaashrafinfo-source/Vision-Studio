@extends('storefront.pages._wrapper', ['pageTitle' => 'Privacy Policy', 'pageIntro' => 'How we handle your personal information.'])

@section('title', 'Privacy Policy')

@section('body')
<p><em>Last updated: {{ now()->format('F j, Y') }}</em></p>

<h3 style="margin-top:28px">What we collect</h3>
<p>When you shop with {{ config('app.name') }}, we collect only what we need to process your order and run the store:</p>
<ul>
    <li><strong>Account info:</strong> name, email, phone number, delivery address</li>
    <li><strong>Order history:</strong> what you bought, when, and how you paid</li>
    <li><strong>Prescription data</strong> (if you order optical lenses): the eye-prescription figures you share with us, kept confidential</li>
    <li><strong>Technical data:</strong> cookies and basic analytics (page views, device type) to keep the site working</li>
</ul>

<h3 style="margin-top:28px">How we use it</h3>
<p>To fulfill your order, communicate about it, handle returns, and improve the store. We do not sell or rent your data to third parties.</p>

<h3 style="margin-top:28px">Who we share with</h3>
<p>Only with service providers who directly help us run the store:</p>
<ul>
    <li>Our courier partner (Pathao, Steadfast, etc.) — delivery address + phone only, for the shipment</li>
    <li>Our payment processor (bKash, Nagad, Visa/Mastercard gateway) — payment details only</li>
    <li>Government authorities when legally required</li>
</ul>

<h3 style="margin-top:28px">Your rights</h3>
<p>You can:</p>
<ul>
    <li>Access or update your account details at any time via <a href="{{ route('account') }}">My Account</a></li>
    <li>Request a full copy of your data</li>
    <li>Request deletion of your account</li>
</ul>
<p>Email <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'hello@visionstudio.com.bd') }}">{{ \App\Models\SiteSetting::get('contact_email', 'hello@visionstudio.com.bd') }}</a> for data requests.</p>

<h3 style="margin-top:28px">Security</h3>
<p>Passwords are stored hashed. Payment data is processed by the gateway directly — we never see or store card numbers.</p>

<h3 style="margin-top:28px">Cookies</h3>
<p>We use essential cookies to keep you signed in and remember your cart. Blocking these may break basic site features.</p>

<h3 style="margin-top:28px">Changes</h3>
<p>We'll post any material changes to this policy on this page and update the "Last updated" date above.</p>
@endsection

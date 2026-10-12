@extends('storefront.pages._wrapper', ['pageTitle' => 'Need Help?', 'pageIntro' => 'We\'re here to help, 7 days a week.'])

@section('title', 'Help & Contact')

@section('body')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:28px">
    <div style="border:1px solid var(--line);border-radius:14px;padding:20px">
        <h3 style="margin:0 0 8px;font-size:16px">Call us</h3>
        <p class="sec-sub" style="margin:0 0 10px;font-size:13px">10am – 9pm, Saturday to Thursday</p>
        <a href="tel:{{ \App\Models\SiteSetting::get('contact_phone', '+880 1719-686459') }}" style="font-size:17px;font-weight:600;color:var(--blue);text-decoration:none">{{ \App\Models\SiteSetting::get('contact_phone', '+880 1719-686459') }}</a>
    </div>
    <div style="border:1px solid var(--line);border-radius:14px;padding:20px">
        <h3 style="margin:0 0 8px;font-size:16px">Email us</h3>
        <p class="sec-sub" style="margin:0 0 10px;font-size:13px">We reply within 24 hours</p>
        <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'hello@visionstudio.com.bd') }}" style="font-size:17px;font-weight:600;color:var(--blue);text-decoration:none">{{ \App\Models\SiteSetting::get('contact_email', 'hello@visionstudio.com.bd') }}</a>
    </div>
</div>

<h3>Quick links</h3>
<ul>
    <li><a href="{{ route('faq') }}">Frequently Asked Questions</a> — most common questions answered</li>
    <li><a href="{{ route('shipping') }}">Shipping & Delivery</a> — delivery times and fees across Bangladesh</li>
    <li><a href="{{ route('returns') }}">Returns & Refunds</a> — our 7-day return policy</li>
    <li><a href="{{ route('stores') }}">Store Locator</a> — visit us in person</li>
</ul>

<h3 style="margin-top:28px">Order help</h3>
<p>Signed in? Check your order status, download invoices and start a return from <a href="{{ route('account') }}">My Account</a>.</p>
@endsection

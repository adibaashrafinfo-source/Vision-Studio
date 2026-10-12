@extends('storefront.pages._wrapper', ['pageTitle' => 'Store Locator', 'pageIntro' => 'Visit us in person for a free fitting and expert advice.'])

@section('title', 'Store Locator')

@section('body')
<div style="border:1px solid var(--line);border-radius:14px;padding:24px;margin-bottom:16px">
    <h3 style="margin:0 0 6px;font-size:18px">Vision Studio — Panthapath Showroom</h3>
    <p class="sec-sub" style="margin:0 0 14px">Our flagship store in central Dhaka.</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:14px">
        <div>
            <strong>Address</strong><br>
            {{ $address ?: 'Level 3, Bashundhara City, Panthapath, Dhaka 1205' }}
        </div>
        <div>
            <strong>Hours</strong><br>
            Saturday–Thursday: 10am–9pm<br>
            Friday: 2pm–9pm
        </div>
        <div>
            <strong>Phone</strong><br>
            <a href="tel:{{ $phone ?: '+880 1719-686459' }}">{{ $phone ?: '+880 1719-686459' }}</a>
        </div>
        <div>
            <strong>Email</strong><br>
            <a href="mailto:{{ $email ?: 'hello@visionstudio.com.bd' }}">{{ $email ?: 'hello@visionstudio.com.bd' }}</a>
        </div>
    </div>
    <p class="sec-sub" style="margin-top:16px;font-size:13px">In-person services: free prescription check, frame fitting, contact lens trial, warranty claims.</p>
</div>

<p style="margin-top:28px"><em>More locations coming soon in Chattogram and Sylhet.</em></p>
@endsection

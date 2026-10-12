@extends('storefront.pages._wrapper', ['pageTitle' => 'Returns & Refunds', 'pageIntro' => 'Our 7-day hassle-free return policy.'])

@section('title', 'Returns & Refunds')

@section('body')
<h3>7-day returns</h3>
<p>You can return any unused product within 7 days of delivery for a full refund or exchange.</p>

<h3 style="margin-top:28px">Conditions</h3>
<ul>
    <li>The product must be unused, unwashed and in its original packaging with all tags and accessories.</li>
    <li>Prescription-cut lenses are non-returnable (the frame can still be returned if the lenses haven't been cut yet).</li>
    <li>Contact lens boxes can only be returned if the seal is unbroken.</li>
    <li>Sale items marked "final sale" cannot be returned.</li>
</ul>

<h3 style="margin-top:28px">How to start a return</h3>
<ol>
    <li>Email us at <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'hello@visionstudio.com.bd') }}">{{ \App\Models\SiteSetting::get('contact_email', 'hello@visionstudio.com.bd') }}</a> or call <a href="tel:{{ \App\Models\SiteSetting::get('contact_phone', '+880 1719-686459') }}">{{ \App\Models\SiteSetting::get('contact_phone', '+880 1719-686459') }}</a> with your order number and reason.</li>
    <li>We'll arrange a courier pickup from your address, or you can drop the product off at our showroom.</li>
    <li>Refund is issued within 5–7 business days of us receiving the product, back to your original payment method (bKash/Nagad/card) or as cash for COD orders.</li>
</ol>

<h3 style="margin-top:28px">Damaged or wrong items</h3>
<p>If your order arrives damaged or we sent the wrong item, contact us within 48 hours of delivery and we'll replace it at no cost.</p>
@endsection

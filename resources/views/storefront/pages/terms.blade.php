@extends('storefront.pages._wrapper', ['pageTitle' => 'Terms of Service', 'pageIntro' => 'The rules of using this site.'])

@section('title', 'Terms of Service')

@section('body')
<p><em>Last updated: {{ now()->format('F j, Y') }}</em></p>

<h3 style="margin-top:28px">1. Agreement</h3>
<p>By using {{ config('app.name') }}, you agree to these terms. If you don't agree, please don't use the site.</p>

<h3 style="margin-top:28px">2. Account responsibility</h3>
<p>You're responsible for keeping your password confidential and for everything that happens under your account. Let us know immediately if you suspect unauthorized use.</p>

<h3 style="margin-top:28px">3. Product information</h3>
<p>We try to describe products accurately — colours, measurements, and prescription compatibility — but minor variations may occur. If something arrives materially different from the description, our return policy covers you.</p>

<h3 style="margin-top:28px">4. Pricing and payment</h3>
<p>All prices are in Bangladeshi Taka (৳) and include VAT where applicable. We reserve the right to correct pricing errors; if the price for a product you ordered changes before shipment, we'll contact you before processing.</p>

<h3 style="margin-top:28px">5. Order acceptance</h3>
<p>An order confirmation email means we've received your order, not that we've accepted it. We may decline or cancel orders in cases of stock issues, pricing errors, or suspected fraud, with a full refund.</p>

<h3 style="margin-top:28px">6. Prescriptions</h3>
<p>Prescription lens orders rely on the prescription you provide. We're not liable for discomfort caused by an incorrect prescription — please verify with your eye doctor before placing an order.</p>

<h3 style="margin-top:28px">7. Intellectual property</h3>
<p>All content on this site (text, images, logos, design) is owned by {{ config('app.name') }} or our brand partners and is protected by copyright. Don't copy or reuse without permission.</p>

<h3 style="margin-top:28px">8. Limitation of liability</h3>
<p>To the extent allowed by law, our total liability for any claim related to a purchase is limited to the amount you paid for that order.</p>

<h3 style="margin-top:28px">9. Governing law</h3>
<p>These terms are governed by the laws of Bangladesh. Any disputes will be resolved in the courts of Dhaka.</p>

<h3 style="margin-top:28px">10. Contact</h3>
<p>Questions about these terms? Email <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'hello@visionstudio.com.bd') }}">{{ \App\Models\SiteSetting::get('contact_email', 'hello@visionstudio.com.bd') }}</a>.</p>
@endsection

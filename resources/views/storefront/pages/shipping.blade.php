@extends('storefront.pages._wrapper', ['pageTitle' => 'Shipping & Delivery', 'pageIntro' => 'How, when and where we deliver.'])

@section('title', 'Shipping & Delivery')

@section('body')
<h3>Where we deliver</h3>
<p>We ship to every district in Bangladesh. Orders are packed and dispatched Saturday to Thursday; Friday orders go out on Saturday.</p>

<h3 style="margin-top:28px">Delivery times</h3>
<ul>
    <li><strong>Dhaka city:</strong> 1–2 business days</li>
    <li><strong>Chattogram, Sylhet, Khulna, Rajshahi:</strong> 2–4 business days</li>
    <li><strong>Other districts:</strong> 3–5 business days</li>
</ul>

<h3 style="margin-top:28px">Shipping fees</h3>
<ul>
    <li><strong>Free shipping</strong> on orders over ৳2,000</li>
    <li><strong>৳60 flat rate</strong> for orders below ৳2,000, anywhere in Bangladesh</li>
</ul>

<h3 style="margin-top:28px">Tracking your order</h3>
<p>Once your order ships, you'll receive an email with a tracking link. Signed-in customers can also view live order status in <a href="{{ route('account') }}">My Account</a>.</p>

<h3 style="margin-top:28px">Prescription lens orders</h3>
<p>Products that require custom lenses add 2–3 extra business days for the lens cutting and fitting. Our team will contact you to collect your prescription after you order.</p>
@endsection

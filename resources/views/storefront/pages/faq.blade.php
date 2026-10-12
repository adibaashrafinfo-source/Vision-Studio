@extends('storefront.pages._wrapper', ['pageTitle' => 'Frequently Asked Questions', 'pageIntro' => 'Common questions about our products, orders and services.'])

@push('title', 'FAQ')
@section('title', 'FAQ')

@section('body')
<div x-data="{open:null}">
    @php
        $faqs = [
            ['Do you deliver outside Dhaka?', 'Yes, we deliver nationwide. Dhaka orders arrive in 1–2 business days, and outside Dhaka usually in 3–5 business days.'],
            ['Are your Ray-Ban and Oakley products authentic?', 'All branded products are 100% authentic and come with the brand\'s original packaging, authenticity card and warranty.'],
            ['How do I get my prescription lenses fitted?', 'After placing an order for an optical frame, our team will contact you to collect your prescription. You can also visit our showroom for a free in-person fitting.'],
            ['What is your return policy?', 'Unused products in original packaging can be returned within 7 days. See our Returns & Refunds page for full details.'],
            ['Which payment methods do you accept?', 'bKash, Nagad, Visa / Mastercard / Amex, and Cash on Delivery across Bangladesh.'],
            ['Do you offer a warranty?', 'Yes — all branded frames carry the manufacturer\'s warranty (typically 1 year). In-house Vision Studio frames have a 6-month warranty.'],
            ['Can I change or cancel my order after placing it?', 'Orders can be changed or cancelled free of charge within 2 hours of placement, before shipping. Contact us as soon as possible.'],
            ['Do you offer contact lens fitting?', 'Yes, we offer free contact lens fitting consultations at our showroom. We stock Acuvue, Bausch + Lomb and other trusted brands.'],
        ];
    @endphp
    @foreach ($faqs as $i => $faq)
        <div style="border:1px solid var(--line);border-radius:12px;margin-bottom:12px;overflow:hidden">
            <button type="button" @click="open=(open==={{ $i }}?null:{{ $i }})" style="width:100%;text-align:left;background:transparent;border:0;padding:16px 20px;font-weight:600;font-size:15px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;color:var(--text);font-family:var(--ui)">
                {{ $faq[0] }}
                <span x-text="open==={{ $i }} ? '−' : '+'" style="font-size:22px;color:var(--muted)"></span>
            </button>
            <div x-show="open==={{ $i }}" x-cloak style="padding:0 20px 18px;color:var(--muted)">{{ $faq[1] }}</div>
        </div>
    @endforeach
</div>
<style>[x-cloak]{display:none !important}</style>
@endsection

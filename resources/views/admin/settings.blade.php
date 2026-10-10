@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="ph-head">
        <div><h1>Settings</h1><p>General store configuration — business info, contact, shipping and payments.</p></div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')
        <div class="set-grid">
            <div class="card">
                <h3 class="sec-ttl">Basic Store Information</h3>
                <p class="sec-sub">Your store name and primary business details.</p>
                <div class="field"><label>Store Name</label><input name="store_name" value="{{ old('store_name', $s['store_name'] ?? config('app.name')) }}"></div>
                <div class="f2">
                    <div class="field"><label>Currency Code</label><input name="currency" value="{{ old('currency', $s['currency'] ?? 'BDT') }}"></div>
                    <div class="field"><label>Currency Symbol</label><input name="currency_symbol" value="{{ old('currency_symbol', $s['currency_symbol'] ?? '৳') }}"></div>
                </div>
            </div>

            <div class="card">
                <h3 class="sec-ttl">Contact Information</h3>
                <p class="sec-sub">Used in the footer and on order communications.</p>
                <div class="field"><label>Email</label><input type="email" name="store_email" value="{{ old('store_email', $s['store_email'] ?? '') }}"></div>
                <div class="field"><label>Phone</label><input name="store_phone" value="{{ old('store_phone', $s['store_phone'] ?? '') }}"></div>
                <div class="field"><label>Address</label><input name="store_address" value="{{ old('store_address', $s['store_address'] ?? '') }}"></div>
            </div>

            <div class="card">
                <h3 class="sec-ttl">Shipping</h3>
                <div class="f2">
                    <div class="field"><label>Default Shipping Fee (৳)</label><input type="number" step="0.01" name="shipping_fee" value="{{ old('shipping_fee', $s['shipping_fee'] ?? 60) }}"></div>
                    <div class="field"><label>Free Shipping Over (৳)</label><input type="number" step="0.01" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $s['free_shipping_threshold'] ?? 2000) }}"></div>
                </div>
            </div>

            <div class="card" x-data="{ cod: {{ ($s['cod_enabled'] ?? 1) ? 'true':'false' }}, bkash: {{ ($s['bkash_enabled'] ?? 1) ? 'true':'false' }}, nagad: {{ ($s['nagad_enabled'] ?? 1) ? 'true':'false' }} }">
                <h3 class="sec-ttl">Payment Methods</h3>
                <p class="sec-sub">Toggle the methods available at checkout.</p>
                <div class="toggle-card" style="margin-bottom:10px"><div class="tc-b"><b>Cash on Delivery</b><p>Pay when the order arrives.</p></div><span class="tw" :class="cod && 'on'" @click="cod=!cod"><i></i></span><input type="hidden" name="cod_enabled" :value="cod?1:0"></div>
                <div class="toggle-card" style="margin-bottom:10px"><div class="tc-b"><b>bKash</b><p>Mobile financial service.</p></div><span class="tw" :class="bkash && 'on'" @click="bkash=!bkash"><i></i></span><input type="hidden" name="bkash_enabled" :value="bkash?1:0"></div>
                <div class="toggle-card"><div class="tc-b"><b>Nagad</b><p>Mobile financial service.</p></div><span class="tw" :class="nagad && 'on'" @click="nagad=!nagad"><i></i></span><input type="hidden" name="nagad_enabled" :value="nagad?1:0"></div>
            </div>
        </div>
        <div class="save-bar"><button type="submit" class="btn btn-primary">Save Settings</button></div>
    </form>
@endsection

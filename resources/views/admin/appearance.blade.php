@extends('layouts.admin')

@section('title', 'Appearance')

@php
    $colors = [
        'theme_navy' => ['Navy / Primary', $defaults['theme_navy']],
        'theme_blue' => ['Blue / Accent', $defaults['theme_blue']],
        'theme_blue_bright' => ['Bright Blue / Hover', $defaults['theme_blue_bright']],
        'theme_sale' => ['Sale / Alert', $defaults['theme_sale']],
        'theme_paper' => ['Paper / Background', $defaults['theme_paper']],
    ];
@endphp

@section('content')
<div x-data="{ logo: @js(($s['logo_path'] ?? null) ? asset('storage/'.$s['logo_path']) : asset('admin/logo.jpg')) }">
    <div class="ph-head">
        <div><h1>Appearance</h1><p>Control your brand logo and the colours used across the live storefront.</p></div>
    </div>

    <form method="POST" action="{{ route('admin.appearance.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="set-grid">
            <div class="card">
                <h3 class="sec-ttl">Brand Logo</h3>
                <p class="sec-sub">Shown in the storefront header and admin sidebar.</p>
                <div class="upload-dash">
                    <div class="lab">Logo</div>
                    <div class="box"><img :src="logo" alt="logo"></div>
                    <div class="rec">PNG or SVG, transparent background recommended</div>
                </div>
                <label class="ubox" style="cursor:pointer;margin-top:12px"><svg class="icon" viewBox="0 0 24 24"><path d="M12 16V4"></path><path d="M7 9l5-5 5 5"></path><path d="M4 20h16"></path></svg><b>Upload new logo</b><input type="file" name="logo" accept="image/*" class="hide" @change="logo=URL.createObjectURL($event.target.files[0])"></label>
            </div>

            <div class="card">
                <h3 class="sec-ttl">Theme Colours</h3>
                <p class="sec-sub">These feed the storefront CSS variables. Use hex values.</p>
                @foreach ($colors as $key => [$label, $default])
                    @php $val = $s[$key] ?? $default; @endphp
                    <div class="swatchrow">
                        <span class="sw" style="background:{{ $val }}" x-ref="sw_{{ $key }}"></span>
                        <div class="lab"><b>{{ $label }}</b><span>{{ $key }}</span></div>
                        <input type="color" name="{{ $key }}" value="{{ $val }}" @input="$refs.sw_{{ $key }}.style.background=$event.target.value" style="width:46px;height:38px;padding:3px">
                    </div>
                @endforeach
                <p class="sec-sub" style="margin-top:10px">Tip: keep Navy dark and Blue mid-tone for best contrast.</p>
            </div>
        </div>
        <div class="save-bar"><button type="submit" class="btn btn-primary">Save Appearance</button></div>
    </form>
</div>
@endsection

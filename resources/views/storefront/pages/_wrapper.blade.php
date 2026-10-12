{{-- Shared chrome for every static content page --}}
@extends('layouts.storefront')

@section('content')
<div class="wrap" style="padding:32px 20px;max-width:860px">
    <div class="crumb">
        <a href="{{ route('home') }}">Home</a><span class="sep">/</span>
        <span style="color:var(--text)">{{ $pageTitle }}</span>
    </div>
    <div style="background:var(--surface);border:1px solid var(--line);border-radius:18px;padding:40px 44px;margin-top:16px">
        <h1 style="font-family:var(--ds);font-weight:600;font-size:32px;margin:0 0 8px;color:var(--navy)">{{ $pageTitle }}</h1>
        @isset($pageIntro)<p class="sec-sub" style="margin:0 0 24px">{{ $pageIntro }}</p>@endisset
        <div class="page-body" style="line-height:1.75;color:var(--text);font-size:15px">
            @yield('body')
        </div>
    </div>
</div>
@endsection

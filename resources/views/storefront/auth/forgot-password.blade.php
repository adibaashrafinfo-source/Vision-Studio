@extends('layouts.storefront')

@section('title', 'Forgot Password')

@section('content')
<div class="wrap" style="padding:48px 20px;display:flex;justify-content:center">
    <div style="width:100%;max-width:440px;background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:34px">
        <h1 style="font-size:26px;margin-bottom:6px">Reset your password</h1>
        <p class="sec-sub" style="margin-bottom:22px">Enter your email and we'll send you a reset link.</p>

        @if (session('status'))
            <div style="background:#f0fdf4;border-left:3px solid #16a34a;border-radius:10px;padding:11px 14px;margin-bottom:18px;color:#166534;font-size:13px">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div style="background:var(--surface-tint);border-left:3px solid var(--sale);border-radius:10px;padding:11px 14px;margin-bottom:18px"><ul style="margin:0;padding-left:18px;color:var(--sale);font-size:13px">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('customer.password.email') }}">
            @csrf
            <div class="field" style="margin-bottom:18px"><label style="display:block;font-size:13px;font-weight:600;margin-bottom:7px">Email</label><input type="email" name="email" value="{{ old('email') }}" required autofocus style="width:100%;border:1px solid var(--line);border-radius:11px;padding:12px 14px;font-family:var(--ui)"></div>
            <button type="submit" class="btn btn-primary" style="width:100%">Send reset link</button>
        </form>
        <p class="sec-sub" style="text-align:center;margin-top:20px"><a href="{{ route('customer.login') }}">Back to sign in</a></p>
    </div>
</div>
@endsection

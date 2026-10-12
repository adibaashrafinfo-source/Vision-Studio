@extends('layouts.storefront')

@section('title', 'Sign In')

@section('content')
<div class="wrap" style="padding:48px 20px;display:flex;justify-content:center">
    <div style="width:100%;max-width:440px;background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:34px">
        <h1 style="font-size:26px;margin-bottom:6px">Sign in</h1>
        <p class="sec-sub" style="margin-bottom:22px">Welcome back to {{ config('app.name') }}.</p>

        @if ($errors->any())
            <div style="background:var(--surface-tint);border-left:3px solid var(--sale);border-radius:10px;padding:11px 14px;margin-bottom:18px"><ul style="margin:0;padding-left:18px;color:var(--sale);font-size:13px">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('customer.login.attempt') }}">
            @csrf
            <div class="field" style="margin-bottom:14px"><label style="display:block;font-size:13px;font-weight:600;margin-bottom:7px">Email</label><input type="email" name="email" value="{{ old('email') }}" required autofocus style="width:100%;border:1px solid var(--line);border-radius:11px;padding:12px 14px;font-family:var(--ui)"></div>
            <div class="field" style="margin-bottom:14px"><label style="display:block;font-size:13px;font-weight:600;margin-bottom:7px">Password</label><input type="password" name="password" required style="width:100%;border:1px solid var(--line);border-radius:11px;padding:12px 14px;font-family:var(--ui)"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)"><input type="checkbox" name="remember" value="1"> Remember me</label>
                <a href="{{ route('customer.password.request') }}" style="font-size:13px;color:var(--blue)">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Sign in</button>
        </form>
        <p class="sec-sub" style="text-align:center;margin-top:20px">New here? <a href="{{ route('customer.register') }}">Create an account</a></p>
    </div>
</div>
@endsection

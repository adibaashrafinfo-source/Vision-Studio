@extends('layouts.storefront')

@section('title', 'Reset Password')

@section('content')
<div class="wrap" style="padding:48px 20px;display:flex;justify-content:center">
    <div style="width:100%;max-width:440px;background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:34px">
        <h1 style="font-size:26px;margin-bottom:6px">Set a new password</h1>
        <p class="sec-sub" style="margin-bottom:22px">Choose a strong password — at least 8 characters.</p>

        @if ($errors->any())
            <div style="background:var(--surface-tint);border-left:3px solid var(--sale);border-radius:10px;padding:11px 14px;margin-bottom:18px"><ul style="margin:0;padding-left:18px;color:var(--sale);font-size:13px">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('customer.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="field" style="margin-bottom:14px"><label style="display:block;font-size:13px;font-weight:600;margin-bottom:7px">Email</label><input type="email" name="email" value="{{ old('email', $email) }}" required style="width:100%;border:1px solid var(--line);border-radius:11px;padding:12px 14px;font-family:var(--ui)"></div>
            <div class="field" style="margin-bottom:14px"><label style="display:block;font-size:13px;font-weight:600;margin-bottom:7px">New password</label><input type="password" name="password" required autofocus style="width:100%;border:1px solid var(--line);border-radius:11px;padding:12px 14px;font-family:var(--ui)"></div>
            <div class="field" style="margin-bottom:18px"><label style="display:block;font-size:13px;font-weight:600;margin-bottom:7px">Confirm password</label><input type="password" name="password_confirmation" required style="width:100%;border:1px solid var(--line);border-radius:11px;padding:12px 14px;font-family:var(--ui)"></div>
            <button type="submit" class="btn btn-primary" style="width:100%">Reset password</button>
        </form>
    </div>
</div>
@endsection

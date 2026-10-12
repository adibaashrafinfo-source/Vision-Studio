<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password — {{ config('app.name') }} Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--navy:#0A1A42;--blue:#0C3F93;--paper:#F4F6FB;--surface:#FFFFFF;--line:#E2E7F2;--text:#0A1A42;--muted:#6B7693;--sale:#E0314A;--ds:"Fraunces",Georgia,serif;--ui:"Work Sans",system-ui,sans-serif;}
        *{box-sizing:border-box}body{margin:0;font-family:var(--ui);color:var(--text);background:var(--paper);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .wrap{width:100%;max-width:440px;background:#fff;border:1px solid var(--line);border-radius:22px;padding:34px;box-shadow:0 30px 80px -40px rgba(10,26,66,.45)}
        h1{font-family:var(--ds);font-weight:600;font-size:26px;margin:0 0 6px}
        p.sub{color:var(--muted);font-size:14px;margin:0 0 22px}
        .field{margin-bottom:16px}label{display:block;font-size:13px;font-weight:600;margin-bottom:7px}
        input{width:100%;border:1px solid var(--line);border-radius:11px;padding:12px 14px;font-family:var(--ui);font-size:14px}
        .btn{background:var(--blue);color:#fff;border:0;border-radius:11px;padding:12px 18px;font-weight:600;cursor:pointer;width:100%;font-family:var(--ui)}
        .err{background:#fef2f2;border-left:3px solid var(--sale);border-radius:10px;padding:11px 14px;margin-bottom:18px;color:var(--sale);font-size:13px}
        .err ul{margin:0;padding-left:18px}
    </style>
</head>
<body>
<div class="wrap">
    <h1>Set a new password</h1>
    <p class="sub">Choose a strong password — at least 8 characters.</p>

    @if ($errors->any())<div class="err"><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $email) }}" required></div>
        <div class="field"><label>New password</label><input type="password" name="password" required autofocus></div>
        <div class="field"><label>Confirm password</label><input type="password" name="password_confirmation" required></div>
        <button class="btn" type="submit">Reset password</button>
    </form>
</div>
</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in — {{ config('app.name') }} Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --navy:#0A1A42;--blue:#0C3F93;--blue-bright:#2F6FD6;--paper:#F4F6FB;
            --surface:#FFFFFF;--surface-tint:#EEF1FA;--line:#E2E7F2;--text:#0A1A42;
            --muted:#6B7693;--sale:#E0314A;--on-navy-muted:#9AA7CD;
            --ds:"Fraunces",Georgia,serif;--ui:"Work Sans",system-ui,-apple-system,sans-serif;
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:var(--ui);color:var(--text);background:var(--paper);
            -webkit-font-smoothing:antialiased;line-height:1.45;min-height:100vh;
            display:flex;align-items:center;justify-content:center;padding:24px}
        .wrap{width:100%;max-width:940px;background:var(--surface);border:1px solid var(--line);
            border-radius:22px;overflow:hidden;display:grid;grid-template-columns:1fr 1fr;
            box-shadow:0 30px 80px -40px rgba(10,26,66,.45)}
        /* brand panel */
        .brand{background:linear-gradient(180deg,#0A1A42,#0B1E4C 55%,#09173E);color:#EAF0FF;
            padding:46px 42px;display:flex;flex-direction:column;justify-content:space-between;
            position:relative;overflow:hidden}
        .brand .deco{position:absolute;right:-60px;top:-60px;width:240px;height:240px;
            border-radius:50%;background:rgba(47,111,214,.18)}
        .brand .deco2{position:absolute;left:-40px;bottom:-50px;width:160px;height:160px;
            border-radius:50%;background:rgba(255,255,255,.05)}
        .logo{display:flex;align-items:center;gap:12px;position:relative;z-index:1}
        .logo .mk{width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,.1);
            display:flex;align-items:center;justify-content:center;font-family:var(--ds);
            font-weight:700;font-size:19px;color:#fff}
        .logo b{font-family:var(--ds);font-weight:700;font-size:19px;color:#fff;display:block;line-height:1.1}
        .logo span{font-size:10.5px;color:var(--on-navy-muted);letter-spacing:.08em;text-transform:uppercase}
        .brand .lead{position:relative;z-index:1}
        .brand h1{font-family:var(--ds);font-weight:600;font-size:30px;letter-spacing:-.02em;
            color:#fff;margin:0 0 12px}
        .brand p{color:var(--on-navy-muted);font-size:14px;margin:0;max-width:300px}
        .brand .feats{display:flex;flex-direction:column;gap:11px;position:relative;z-index:1;margin-top:8px}
        .brand .feat{display:flex;align-items:center;gap:10px;font-size:13px;color:#C9D4F0}
        .brand .feat svg{width:18px;height:18px;stroke:var(--blue-bright);stroke-width:1.8;fill:none;
            stroke-linecap:round;stroke-linejoin:round;flex:none}
        /* form panel */
        .form{padding:46px 42px;display:flex;flex-direction:column;justify-content:center}
        .form h2{font-family:var(--ds);font-weight:600;font-size:25px;margin:0 0 6px}
        .form .sub{color:var(--muted);font-size:13.5px;margin:0 0 26px}
        .field{margin-bottom:16px}
        .field label{display:block;font-size:12.5px;font-weight:600;color:var(--text);margin-bottom:7px}
        .field input{width:100%;border:1px solid var(--line);background:var(--surface);border-radius:11px;
            padding:12px 14px;font-family:var(--ui);font-size:14px;color:var(--text);outline:none;
            transition:border-color .15s,box-shadow .15s}
        .field input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(47,111,214,.14)}
        .row{display:flex;align-items:center;justify-content:space-between;margin:4px 0 22px}
        .check{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);cursor:pointer}
        .check input{accent-color:var(--blue);width:15px;height:15px}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;
            font-family:var(--ui);font-weight:600;font-size:15px;border-radius:11px;border:0;
            padding:13px 20px;cursor:pointer;background:var(--blue);color:#fff;transition:background .15s,transform .15s}
        .btn:hover{background:var(--blue-bright);transform:translateY(-1px)}
        .alert{background:var(--surface-tint);border:1px solid var(--line);border-left:3px solid var(--sale);
            border-radius:10px;padding:11px 14px;margin-bottom:20px}
        .alert ul{margin:0;padding-left:18px}
        .alert li{color:var(--sale);font-size:13px}
        .foot-note{margin-top:24px;font-size:12px;color:var(--muted);text-align:center}
        @media (max-width:760px){
            .wrap{grid-template-columns:1fr;max-width:420px}
            .brand{display:none}
            .form{padding:38px 28px}
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="brand">
            <span class="deco"></span>
            <span class="deco2"></span>
            <div class="logo">
                <div class="mk">VS</div>
                <div>
                    <b>{{ config('app.name') }}</b>
                    <span>Admin Panel</span>
                </div>
            </div>
            <div class="lead">
                <h1>Your store,<br>in focus.</h1>
                <p>Manage products, orders, customers and your storefront — all in one place.</p>
            </div>
            <div class="feats">
                <div class="feat">
                    <svg viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path><path d="M9.5 12l1.8 1.8L15 10"></path></svg>
                    Secure session-based access
                </div>
                <div class="feat">
                    <svg viewBox="0 0 24 24"><path d="M3 7h11v8H3z"></path><path d="M14 10h4l3 3v2h-7z"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle></svg>
                    Real-time catalog &amp; orders
                </div>
                <div class="feat">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M18.4 5.6L17 7M7 17l-1.4 1.4"></path></svg>
                    Controls the live storefront
                </div>
            </div>
        </div>

        <div class="form">
            <h2>Sign in</h2>
            <p class="sub">Welcome back. Enter your credentials to continue.</p>

            @if ($errors->any())
                <div class="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}">
                @csrf
                <div class="field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="username" placeholder="you@visionstudio.com">
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password" placeholder="••••••••">
                </div>
                <div class="row">
                    <label class="check">
                        <input type="checkbox" name="remember" value="1">
                        Remember me
                    </label>
                    <a href="{{ route('admin.password.request') }}" style="font-size:13px;color:var(--blue)">Forgot password?</a>
                </div>
                <button type="submit" class="btn">
                    Sign in
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg>
                </button>
            </form>

            <p class="foot-note">{{ config('app.name') }} Admin · Authorized personnel only</p>
        </div>
    </div>
</body>
</html>

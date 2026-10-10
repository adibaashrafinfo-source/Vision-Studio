<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — {{ config('app.name') }} Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--navy:#0A1A42;--blue:#0C3F93;--blue-bright:#2F6FD6;--paper:#F4F6FB;--surface:#fff;
            --line:#E2E7F2;--text:#0A1A42;--muted:#6B7693;
            --ds:"Fraunces",Georgia,serif;--ui:"Work Sans",system-ui,sans-serif}
        *{box-sizing:border-box}
        body{margin:0;font-family:var(--ui);background:var(--paper);color:var(--text);min-height:100vh;
            display:flex;align-items:center;justify-content:center;padding:24px}
        .card{background:var(--surface);border:1px solid var(--line);border-radius:20px;padding:40px;
            max-width:520px;width:100%;box-shadow:0 24px 70px -44px rgba(10,26,66,.4);text-align:center}
        .mk{width:52px;height:52px;border-radius:13px;background:var(--navy);color:#fff;display:flex;
            align-items:center;justify-content:center;font-family:var(--ds);font-weight:700;font-size:21px;margin:0 auto 18px}
        h1{font-family:var(--ds);font-weight:600;font-size:26px;margin:0 0 8px}
        p{color:var(--muted);font-size:14px;margin:0 0 6px}
        .name{color:var(--text);font-weight:600}
        .note{background:var(--paper);border:1px dashed var(--line);border-radius:12px;padding:14px 16px;
            margin:22px 0;font-size:13px;color:var(--muted)}
        .btn{display:inline-flex;align-items:center;gap:8px;font-family:var(--ui);font-weight:600;font-size:14px;
            border-radius:11px;border:0;padding:12px 22px;cursor:pointer;background:var(--blue);color:#fff;
            transition:background .15s}
        .btn:hover{background:var(--blue-bright)}
    </style>
</head>
<body>
    <div class="card">
        <div class="mk">VS</div>
        <h1>You're signed in</h1>
        <p>Welcome back, <span class="name">{{ auth('admin')->user()->name }}</span>.</p>
        <p>{{ auth('admin')->user()->email }} · {{ ucwords(str_replace('_', ' ', auth('admin')->user()->role)) }}</p>

        <div class="note">
            Admin authentication is working. The full dashboard and all admin panel
            pages are built in Step 3, matching the reference design 1:1.
        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn">
                Sign out
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
            </button>
        </form>
    </div>
</body>
</html>

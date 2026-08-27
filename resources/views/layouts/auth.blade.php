<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MenuKasir')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }
        :root {
            --primary: #f97316;
            --primary-dark: #ea6c0a;
            --border: #e2e8f0;
            --radius: 16px;
            --transition: .2s cubic-bezier(.4,0,.2,1);
        }
        .auth-card {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 32px 80px rgba(0,0,0,.4);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            animation: fadeUp .5s ease;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(32px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .auth-header {
            background: linear-gradient(135deg, #f97316, #fb923c, #fbbf24);
            padding: 36px 40px;
            text-align: center;
            color: #fff;
        }
        .auth-logo { font-size: 3rem; margin-bottom: 12px; display: block; filter: drop-shadow(0 4px 12px rgba(0,0,0,.2)); }
        .auth-header h1 { font-size: 1.5rem; font-weight: 900; margin-bottom: 4px; }
        .auth-header p  { font-size: .875rem; opacity: .85; }
        .auth-body { padding: 36px 40px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: .85rem; font-weight: 700; color: #374151; margin-bottom: 8px; }
        .form-control {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid #e5e7eb;
            border-radius: var(--radius);
            font-size: .95rem;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
            background: #f9fafb;
            color: #111827;
        }
        .form-control:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(249,115,22,.12); }
        .form-control.is-invalid { border-color: #ef4444; }
        .error-msg { font-size: .8rem; color: #ef4444; margin-top: 5px; }
        .form-check { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; }
        .form-check input { width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer; }
        .form-check label { font-size: .875rem; color: #6b7280; cursor: pointer; }
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary), #fb923c);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(249,115,22,.4); }
        .btn-submit:active { transform: none; }
        .btn-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }
        .alert-danger {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: .875rem;
            margin-bottom: 20px;
        }
        .auth-footer {
            text-align: center;
            padding: 0 40px 28px;
            font-size: .8rem;
            color: #9ca3af;
        }
        .auth-footer a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .spinner {
            width: 18px; height: 18px;
            border: 2.5px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="auth-card">
        @yield('content')
    </div>
</body>
</html>

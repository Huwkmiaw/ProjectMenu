<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MenuKasir') — Self-Order System</title>
    <meta name="description" content="@yield('meta-description', 'Sistem pemesanan mandiri yang mudah dan cepat.')">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           DESIGN TOKENS
        ============================================================ */
        :root {
            --color-primary:      #f97316;
            --color-primary-dark: #ea6c0a;
            --color-primary-light:#fff7ed;
            --color-secondary:    #1e293b;
            --color-accent:       #06b6d4;
            --color-success:      #22c55e;
            --color-warning:      #f59e0b;
            --color-danger:       #ef4444;
            --color-muted:        #94a3b8;
            --color-border:       #e2e8f0;
            --color-bg:           #f8fafc;
            --color-surface:      #ffffff;
            --color-text:         #1e293b;
            --color-text-light:   #64748b;

            --radius-sm:   6px;
            --radius-md:   12px;
            --radius-lg:   20px;
            --radius-xl:   28px;
            --radius-full: 9999px;

            --shadow-sm: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
            --shadow-md: 0 4px 16px rgba(0,0,0,.10);
            --shadow-lg: 0 10px 40px rgba(0,0,0,.13);

            --transition: .2s cubic-bezier(.4,0,.2,1);
            --font: 'Inter', system-ui, sans-serif;
        }

        /* ============================================================
           RESET & BASE
        ============================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font);
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }
        button, input, select, textarea { font-family: inherit; }

        /* ============================================================
           CUSTOMER TOPBAR
        ============================================================ */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--color-border);
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--color-primary);
        }
        .topbar-brand span { font-size: 1.5rem; }
        .topbar-badge {
            background: var(--color-primary-light);
            color: var(--color-primary);
            font-size: .72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: var(--radius-full);
            border: 1px solid #fed7aa;
        }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }

        /* ============================================================
           CART BUTTON
        ============================================================ */
        .btn-cart {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-full);
            padding: 10px 20px;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }
        .btn-cart:hover { background: var(--color-primary-dark); transform: translateY(-1px); box-shadow: var(--shadow-md); }
        .btn-cart .cart-count {
            position: absolute;
            top: -6px; right: -6px;
            background: #fff;
            color: var(--color-primary);
            font-size: .7rem;
            font-weight: 700;
            width: 20px; height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--color-primary);
        }

        /* ============================================================
           BUTTONS
        ============================================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: var(--radius-md);
            padding: 12px 24px;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            white-space: nowrap;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn-primary { background: var(--color-primary); color: #fff; }
        .btn-primary:hover { background: var(--color-primary-dark); box-shadow: 0 4px 20px rgba(249,115,22,.4); }
        .btn-secondary { background: var(--color-surface); color: var(--color-text); border: 1.5px solid var(--color-border); }
        .btn-secondary:hover { border-color: var(--color-primary); color: var(--color-primary); }
        .btn-success { background: var(--color-success); color: #fff; }
        .btn-success:hover { background: #16a34a; box-shadow: 0 4px 20px rgba(34,197,94,.4); }
        .btn-danger { background: var(--color-danger); color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-warning { background: var(--color-warning); color: #fff; }
        .btn-warning:hover { background: #d97706; }
        .btn-sm { padding: 7px 16px; font-size: .82rem; border-radius: var(--radius-sm); }
        .btn-lg { padding: 16px 32px; font-size: 1rem; border-radius: var(--radius-lg); }
        .btn-full { width: 100%; }
        .btn-outline-primary {
            background: transparent;
            color: var(--color-primary);
            border: 2px solid var(--color-primary);
        }
        .btn-outline-primary:hover { background: var(--color-primary); color: #fff; }

        /* ============================================================
           FORMS
        ============================================================ */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: .875rem; font-weight: 600; color: var(--color-text); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--color-border);
            border-radius: var(--radius-md);
            font-size: .95rem;
            background: var(--color-surface);
            color: var(--color-text);
            transition: var(--transition);
            outline: none;
        }
        .form-control:focus { border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(249,115,22,.12); }
        .form-control::placeholder { color: var(--color-muted); }
        .form-error { font-size: .8rem; color: var(--color-danger); margin-top: 5px; }
        .form-hint { font-size: .8rem; color: var(--color-text-light); margin-top: 5px; }

        /* ============================================================
           CARDS
        ============================================================ */
        .card {
            background: var(--color-surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-border);
            overflow: hidden;
        }
        .card-body { padding: 24px; }
        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .card-title { font-size: 1.05rem; font-weight: 700; }

        /* ============================================================
           BADGES
        ============================================================ */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: .75rem;
            font-weight: 600;
        }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-confirmed{ background: #dbeafe; color: #1d4ed8; }
        .badge-paid     { background: #d1fae5; color: #065f46; }
        .badge-completed{ background: #e0e7ff; color: #3730a3; }
        .badge-cancelled{ background: #fee2e2; color: #991b1b; }
        .badge-dine-in  { background: #fff7ed; color: #c2410c; }
        .badge-take-away{ background: #f0fdf4; color: #166534; }
        .badge-primary  { background: var(--color-primary-light); color: var(--color-primary); }

        /* ============================================================
           ALERTS / FLASH MESSAGES
        ============================================================ */
        .alert {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-size: .9rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
            animation: slideIn .3s ease;
        }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger   { background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }
        .alert-warning  { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .alert-info     { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ============================================================
           CONTAINER
        ============================================================ */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        .container-sm { max-width: 640px; margin: 0 auto; padding: 0 24px; }

        /* ============================================================
           QUANTITY CONTROL
        ============================================================ */
        .qty-control {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid var(--color-border);
            border-radius: var(--radius-md);
            overflow: hidden;
        }
        .qty-btn {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            background: var(--color-bg);
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-text);
            transition: var(--transition);
        }
        .qty-btn:hover { background: var(--color-primary-light); color: var(--color-primary); }
        .qty-input {
            width: 48px; height: 36px;
            text-align: center;
            border: none;
            border-left: 1.5px solid var(--color-border);
            border-right: 1.5px solid var(--color-border);
            font-weight: 600;
            font-size: .95rem;
            background: #fff;
            outline: none;
        }

        /* ============================================================
           LOADING / SPINNER
        ============================================================ */
        .spinner {
            width: 20px; height: 20px;
            border: 2.5px solid rgba(255,255,255,.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .8s linear infinite;
            display: inline-block;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ============================================================
           EMPTY STATE
        ============================================================ */
        .empty-state {
            text-align: center;
            padding: 60px 24px;
            color: var(--color-muted);
        }
        .empty-state-icon { font-size: 4rem; margin-bottom: 16px; }
        .empty-state h3 { font-size: 1.1rem; color: var(--color-text-light); font-weight: 600; margin-bottom: 8px; }
        .empty-state p { font-size: .9rem; }

        /* ============================================================
           UTILITY
        ============================================================ */
        .text-primary { color: var(--color-primary); }
        .text-muted   { color: var(--color-muted); }
        .text-success { color: var(--color-success); }
        .text-danger  { color: var(--color-danger); }
        .text-center  { text-align: center; }
        .font-bold    { font-weight: 700; }
        .font-semibold{ font-weight: 600; }
        .mt-1 { margin-top: 8px; }
        .mt-2 { margin-top: 16px; }
        .mt-3 { margin-top: 24px; }
        .mt-4 { margin-top: 32px; }
        .mb-2 { margin-bottom: 16px; }
        .gap-2 { gap: 16px; }
        .flex { display: flex; }
        .flex-center { display: flex; align-items: center; justify-content: center; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .w-full { width: 100%; }
        .divider { border: none; border-top: 1px solid var(--color-border); margin: 20px 0; }
        .sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); }

        /* ============================================================
           RESPONSIVE
        ============================================================ */
        @media (max-width: 768px) {
            .topbar { padding: 0 16px; }
            .container, .container-sm { padding: 0 16px; }
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- TOPBAR -->
    <header class="topbar">
        <a href="{{ route('welcome') }}" class="topbar-brand">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--color-primary);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            MenuKasir
        </a>

        <div class="topbar-actions">
            @if (session('order_type'))
                <span class="topbar-badge">
                    @if(session('order_type') === 'dine_in')
                        <svg fill="currentColor" viewBox="0 0 73.602 46.542" style="width:16px; height:11px; vertical-align:middle; display:inline-block;"><g><path d="M10.429,4.148c-0.743,0-1.344,0.598-1.344,1.338v8.018c0,1.084-0.807,1.988-1.854,2.14V5.937 c0-0.735-0.602-1.338-1.345-1.338c-0.744,0-1.345,0.603-1.345,1.338v9.707c-1.043-0.152-1.854-1.056-1.854-2.14V5.486 c0-0.74-0.601-1.338-1.343-1.338C0.604,4.148,0,4.746,0,5.486v8.018c0,2.563,2.012,4.669,4.543,4.835v21.749 c0,0.744,0.601,1.342,1.345,1.342c0.743,0,1.345-0.598,1.345-1.342V18.339c2.532-0.166,4.543-2.273,4.543-4.835V5.486 C11.775,4.746,11.175,4.148,10.429,4.148z"></path><path d="M67.94,4.113c-2.329,0-2.154,4.669-2.154,10.426c0,3.801-0.076,7.126,0.542,8.948v16.601c0,0.744,0.601,1.342,1.344,1.342 c0.745,0,1.346-0.598,1.346-1.342V24.675c2.158-1.1,4.584-5.219,4.584-10.136C73.601,8.782,70.272,4.113,67.94,4.113z"></path><path d="M38.17,0C25.276,0,14.817,10.419,14.817,23.272c0,12.853,10.459,23.271,23.353,23.271 c12.901,0,23.359-10.418,23.359-23.271C61.529,10.419,51.071,0,38.17,0z"></path></g></svg>
                        Dine In
                    @else
                        <svg fill="currentColor" viewBox="0 0 463 463" style="width:13px; height:13px; vertical-align:middle; display:inline-block;"><g><path d="M367.372,142.726c-0.413-8.257-7.213-14.726-15.481-14.726H298.12l-12.974-82.169C280.953,19.274,258.396,0,231.49,0 c-26.885,0-49.442,19.274-53.635,45.831L164.881,128H111.11c-8.268,0-15.068,6.469-15.481,14.726l-15.2,304 c-0.211,4.22,1.338,8.396,4.25,11.457S91.685,463,95.911,463h271.18c4.225,0,8.318-1.756,11.23-4.817s4.461-7.236,4.25-11.457 L367.372,142.726z M192.672,48.171C195.708,28.95,212.032,15,231.511,15c19.458,0,35.784,13.95,38.819,33.171L282.935,128 H180.068L192.672,48.171z M367.453,447.845C367.305,448,367.149,448,367.091,448H95.911c-0.059,0-0.214,0-0.362-0.155 c-0.148-0.155-0.14-0.311-0.137-0.369l15.2-304c0.013-0.267,0.233-0.476,0.5-0.476h51.402l-2.421,15.33 c-0.646,4.092,2.147,7.932,6.238,8.578c0.396,0.063,0.79,0.093,1.179,0.093c3.626,0,6.815-2.636,7.399-6.331l2.79-17.67h107.604 l2.79,17.67c0.646,4.091,4.483,6.88,8.578,6.238c4.091-0.646,6.884-4.486,6.238-8.578l-2.42-15.33h51.402 c0.267,0,0.486,0.209,0.5,0.476l15.2,304C367.593,447.534,367.601,447.689,367.453,447.845z"></path><path d="M231.501,192c-4.142,0-7.5,3.357-7.5,7.5V240h-9v-40.5c0-4.143-3.358-7.5-7.5-7.5c-4.142,0-7.5,3.357-7.5,7.5V240h-9 v-40.5c0-4.143-3.358-7.5-7.5-7.5c-4.142,0-7.5,3.357-7.5,7.5v56c0,12.958,10.542,23.5,23.5,23.5h0.5v128.5 c0,4.143,3.358,7.5,7.5,7.5c4.142,0,7.5-3.357,7.5-7.5V279h0.5c12.958,0,23.5-10.542,23.5-23.5v-56 C239.001,195.357,235.643,192,231.501,192z M224.001,255.5c0,4.687-3.813,8.5-8.5,8.5h-16c-4.687,0-8.5-3.813-8.5-8.5V255h33 V255.5z"></path><path d="M287.719,206.754c-0.816-8.162-7.617-14.317-15.82-14.317c-8.767,0-15.899,7.133-15.899,15.899V407.5 c0,4.143,3.358,7.5,7.5,7.5s7.5-3.357,7.5-7.5V359h6.717c6.653,0,13.02-2.837,17.47-7.782s6.6-11.576,5.9-18.191L287.719,206.754 z M284.036,341.186C282.403,343,280.159,344,277.718,344H271V208.336c0-0.496,0.403-0.899,0.899-0.899 c0.464,0,0.849,0.348,0.899,0.853l13.372,126.316C286.427,337.033,285.669,339.37,284.036,341.186z"></path></g></svg>
                        Take Away
                    @endif
                </span>
                <a href="{{ route('welcome') }}" class="btn btn-secondary btn-sm" style="font-size:.78rem; padding:5px 12px;">
                    Ganti Layanan
                </a>
            @endif
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    @if (session('success'))
        <div class="container mt-2">
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        </div>
    @endif
    @if (session('error'))
        <div class="container mt-2">
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        </div>
    @endif

    <!-- MAIN CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer style="text-align:center; padding: 32px 24px; color: var(--color-muted); font-size:.8rem; border-top: 1px solid var(--color-border); margin-top: 48px;">
        &copy; {{ date('Y') }} MenuKasir — Self-Order System
    </footer>

    <script>
        // Auto-dismiss flash messages
        document.querySelectorAll('.alert').forEach(el => {
            setTimeout(() => {
                el.style.transition = 'opacity .4s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            }, 4000);
        });

        // Update cart badge without reload
        function updateCartBadge(count) {
            const badge = document.getElementById('cart-count-badge');
            const btn   = document.getElementById('btn-cart-topbar');
            if (count > 0) {
                if (badge) {
                    badge.textContent = count;
                } else if (btn) {
                    const span = document.createElement('span');
                    span.className = 'cart-count';
                    span.id = 'cart-count-badge';
                    span.textContent = count;
                    btn.appendChild(span);
                }
            } else if (badge) {
                badge.remove();
            }
        }
    </script>

    @stack('scripts')
</body>
</html>

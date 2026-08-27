<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — MenuKasir</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary:       #f97316;
            --primary-dark:  #ea6c0a;
            --primary-light: #fff7ed;
            --secondary:     #1e293b;
            --accent:        #6366f1;
            --success:       #22c55e;
            --warning:       #f59e0b;
            --danger:        #ef4444;
            --muted:         #94a3b8;
            --border:        #e2e8f0;
            --bg:            #f1f5f9;
            --surface:       #ffffff;
            --text:          #1e293b;
            --text-light:    #64748b;
            --sidebar-w:     240px;
            --topbar-h:      64px;
            --radius-sm:     8px;
            --radius-md:     12px;
            --radius-lg:     20px;
            --shadow-sm:     0 1px 3px rgba(0,0,0,.08);
            --shadow-md:     0 4px 16px rgba(0,0,0,.1);
            --transition:    .2s cubic-bezier(.4,0,.2,1);
            --font:          'Inter', system-ui, sans-serif;
        }
        html { scroll-behavior: smooth; }
        body { font-family: var(--font); background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; }
        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font-family: inherit; }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--secondary);
            display: flex;
            flex-direction: column;
            z-index: 200;
            overflow-y: auto;
            transition: transform var(--transition);
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand-icon { font-size: 1.8rem; }
        .sidebar-brand-text  { font-weight: 800; font-size: 1.1rem; color: #fff; line-height: 1.2; }
        .sidebar-brand-text small { display: block; font-size: .65rem; font-weight: 400; color: #94a3b8; text-transform: uppercase; letter-spacing: .08em; }

        .sidebar-section { padding: 16px 12px 8px; }
        .sidebar-section-label { font-size: .65rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .1em; padding: 0 10px; margin-bottom: 6px; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: #94a3b8;
            font-size: .875rem;
            font-weight: 500;
            transition: var(--transition);
            white-space: nowrap;
        }
        .sidebar-link:hover { background: rgba(255,255,255,.07); color: #fff; }
        .sidebar-link.active { background: var(--primary); color: #fff; font-weight: 600; }
        .sidebar-link .link-icon { font-size: 1.1rem; width: 22px; text-align: center; flex-shrink: 0; }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius-sm);
        }
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .9rem;
            flex-shrink: 0;
        }
        .user-name  { font-size: .85rem; font-weight: 600; color: #fff; }
        .user-role  { font-size: .72rem; color: #64748b; }
        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            margin-top: 8px;
            padding: 9px;
            background: rgba(239,68,68,.15);
            color: #fca5a5;
            border: none;
            border-radius: var(--radius-sm);
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-logout:hover { background: rgba(239,68,68,.3); color: #fff; }

        /* ── MAIN AREA ── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .dash-topbar {
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .dash-topbar h1 { font-size: 1.15rem; font-weight: 700; }
        .dash-topbar-right { display: flex; align-items: center; gap: 12px; }

        .main-content { padding: 28px; flex: 1; }

        /* ── STAT CARDS ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px; }
        .stat-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 22px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .stat-icon.orange  { background: #fff7ed; }
        .stat-icon.blue    { background: #eff6ff; }
        .stat-icon.green   { background: #f0fdf4; }
        .stat-icon.purple  { background: #f5f3ff; }
        .stat-value { font-size: 1.7rem; font-weight: 900; line-height: 1; }
        .stat-label { font-size: .8rem; color: var(--text-light); font-weight: 500; margin-top: 4px; }

        /* ── TABLES ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 12px 16px; font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); border-bottom: 2px solid var(--border); text-align: left; white-space: nowrap; }
        tbody td { padding: 14px 16px; font-size: .9rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tbody tr:hover { background: var(--bg); }
        tbody tr:last-child td { border-bottom: none; }

        /* ── BADGES ── */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 999px; font-size: .73rem; font-weight: 600; white-space: nowrap; }
        .badge-pending   { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #dbeafe; color: #1d4ed8; }
        .badge-paid      { background: #d1fae5; color: #065f46; }
        .badge-completed { background: #e0e7ff; color: #3730a3; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-dine-in   { background: #fff7ed; color: #c2410c; }
        .badge-take-away { background: #f0fdf4; color: #166534; }
        .badge-admin     { background: #f5f3ff; color: #5b21b6; }
        .badge-cashier   { background: #ecfeff; color: #0e7490; }

        /* ── BUTTONS ── */
        .btn { display: inline-flex; align-items: center; gap: 6px; border: none; border-radius: var(--radius-sm); padding: 9px 18px; font-size: .85rem; font-weight: 600; cursor: pointer; transition: var(--transition); text-decoration: none; white-space: nowrap; }
        .btn:hover { opacity: .9; transform: translateY(-1px); }
        .btn-primary   { background: var(--primary); color: #fff; }
        .btn-success   { background: var(--success); color: #fff; }
        .btn-warning   { background: var(--warning); color: #fff; }
        .btn-danger    { background: var(--danger);  color: #fff; }
        .btn-secondary { background: var(--surface); color: var(--text); border: 1.5px solid var(--border); }
        .btn-secondary:hover { border-color: var(--primary); color: var(--primary); }
        .btn-sm  { padding: 6px 12px; font-size: .78rem; }
        .btn-xs  { padding: 4px 10px; font-size: .72rem; }
        .btn-full { width: 100%; justify-content: center; }

        /* ── CARDS ── */
        .card { background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .card-title  { font-size: 1rem; font-weight: 700; }
        .card-body   { padding: 24px; }

        /* ── FORMS ── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: .82rem; font-weight: 700; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-size: .9rem; background: #fff; outline: none; transition: var(--transition); }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(249,115,22,.1); }
        .form-error { font-size: .78rem; color: var(--danger); margin-top: 4px; }

        /* ── ALERTS ── */
        .alert { padding: 12px 18px; border-radius: var(--radius-sm); font-size: .875rem; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger  { background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }
        .alert-warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .alert-info    { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }

        /* ── UTILITY ── */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 16px; }
        .gap-1 { gap: 8px; }
        .mt-1 { margin-top: 8px; }
        .mt-2 { margin-top: 16px; }
        .mb-2 { margin-bottom: 16px; }
        .text-muted  { color: var(--muted); }
        .text-right  { text-align: right; }
        .font-bold   { font-weight: 700; }
        .divider { border: none; border-top: 1px solid var(--border); margin: 20px 0; }
        .w-full { width: 100%; }

        /* ── ORDER CARD (Cashier) ── */
        .order-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            padding: 20px;
            transition: var(--transition);
            animation: slideIn .3s ease;
        }
        .order-card:hover { border-color: var(--primary); box-shadow: var(--shadow-md); }
        .order-card.is-new { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .order-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 8px; flex-wrap: wrap; }
        .order-code { font-weight: 800; font-size: 1rem; color: var(--primary); }
        .order-time { font-size: .75rem; color: var(--muted); }
        .order-items-list { font-size: .85rem; color: var(--text-light); margin: 10px 0; line-height: 1.6; }
        .order-total { font-size: 1.05rem; font-weight: 800; color: var(--text); }
        .order-actions { display: flex; gap: 8px; margin-top: 14px; flex-wrap: wrap; }

        /* ── MOBILE SIDEBAR TOGGLE ── */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.3rem;
            cursor: pointer;
            color: var(--text);
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 150;
        }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .main-wrap { margin-left: 0; }
            .sidebar-toggle { display: block; }
            .main-content { padding: 20px 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <span class="sidebar-brand-icon" style="color:var(--primary);">
            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </span>
        <div class="sidebar-brand-text">
            MenuKasir
            <small>{{ auth()->user()->role === 'admin' ? 'Admin Panel' : 'Cashier Panel' }}</small>
        </div>
    </div>

    <div class="sidebar-section">
        @if(auth()->user()->role === 'admin')
            <div class="sidebar-section-label">Admin</div>
            <a href="{{ route('admin.dashboard') }}"  class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="link-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </span> Dashboard
            </a>
            <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span class="link-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </span> Kategori
            </a>
            <a href="{{ route('admin.menus.index') }}"      class="sidebar-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                <span class="link-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </span> Menu
            </a>
            <a href="{{ route('admin.cashiers.index') }}"   class="sidebar-link {{ request()->routeIs('admin.cashiers.*') ? 'active' : '' }}">
                <span class="link-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span> Kasir
            </a>
            <a href="{{ route('admin.reports.index') }}"    class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <span class="link-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span> Laporan
            </a>

            <div class="sidebar-section-label" style="margin-top:16px">Kasir</div>
            <a href="{{ route('cashier.dashboard') }}" class="sidebar-link {{ request()->routeIs('cashier.*') ? 'active' : '' }}">
                <span class="link-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span> Dashboard Kasir
            </a>
        @else
            <div class="sidebar-section-label">Kasir</div>
            <a href="{{ route('cashier.dashboard') }}"     class="sidebar-link {{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}">
                <span class="link-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span> Dashboard
            </a>
            <a href="{{ route('cashier.orders.index') }}"  class="sidebar-link {{ request()->routeIs('cashier.orders.index') ? 'active' : '' }}">
                <span class="link-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </span> Semua Pesanan
            </a>
            <a href="{{ route('cashier.orders.history') }}" class="sidebar-link {{ request()->routeIs('cashier.orders.history') ? 'active' : '' }}">
                <span class="link-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span> Riwayat Transaksi
            </a>
        @endif
    </div>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

<!-- MAIN WRAPPER -->
<div class="main-wrap">
    <!-- TOPBAR -->
    <header class="dash-topbar">
        <div class="flex items-center gap-1">
            <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
            <h1>@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="dash-topbar-right">
            @yield('topbar-actions')
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div style="padding: 0 24px;">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div style="padding: 0 24px;">
            <div class="alert alert-danger">{{ session('error') }}</div>
        </div>
    @endif

    <!-- PAGE CONTENT -->
    <main class="main-content">
        @yield('content')
    </main>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
    }

    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });
</script>

@stack('scripts')
</body>
</html>

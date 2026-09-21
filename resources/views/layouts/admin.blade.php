<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') — MuezzaStore</title>

    {{-- Favicon --}}
    <link rel="icon"            type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon"   type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon"                 href="{{ asset('images/logo.png') }}">

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ── VARIABLES ────────────────────────────────── */
        :root {
            --cyan:      #03AEC6;
            --cyan2:     #029bb5;
            --navy:      #01294D;
            --navy2:     #023a6b;
            --sidebar-w: 260px;
            --header-h:  64px;
            --bg:        #f1f5f9;
            --card-bg:   #ffffff;
            --border:    #e2e8f0;
            --text:      #01294D;
            --gray:      #64748b;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── SIDEBAR ──────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy2) 60%, #016e8a 100%);
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow: hidden;
        }

        /* decorative glow */
        .sidebar::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(3,174,198,0.25) 0%, transparent 70%);
            pointer-events: none;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 1.25rem 1.5rem 1rem;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 0.5rem;
        }

        .sidebar-brand img {
            height: 36px; width: auto; object-fit: contain;
            filter: brightness(0) saturate(100%) invert(62%) sepia(97%) saturate(400%) hue-rotate(155deg) brightness(95%);
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            font-size: 1.125rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #fff;
            line-height: 1;
        }

        .sidebar-brand-text span { color: var(--cyan); }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.625rem 1.5rem 1rem;
            margin-bottom: 0.25rem;
        }

        .sidebar-user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: rgba(3,174,198,0.25);
            border: 2px solid rgba(3,174,198,0.4);
            display: flex; align-items: center; justify-content: center;
            color: var(--cyan); font-size: .8rem; flex-shrink: 0;
        }

        .sidebar-user-name {
            font-size: .8rem; font-weight: 600;
            color: rgba(255,255,255,0.65);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* nav section label */
        .nav-label {
            font-size: .65rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: rgba(255,255,255,0.35);
            padding: .75rem 1.5rem .25rem;
        }

        /* nav links */
        .sidebar nav { padding: 0 0.75rem; flex: 1; overflow-y: auto; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6875rem 0.875rem;
            border-radius: 10px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 600;
            transition: all .2s ease;
            margin-bottom: 2px;
        }

        .nav-item i {
            width: 18px; text-align: center;
            font-size: .9rem; flex-shrink: 0;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(3,174,198,0.30) 0%, rgba(3,174,198,0.15) 100%);
            color: #fff;
            border: 1px solid rgba(3,174,198,0.35);
        }

        .nav-item.active i { color: var(--cyan); }

        .nav-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 0.75rem 0.875rem;
        }

        /* sidebar footer */
        .sidebar-footer {
            padding: 1rem 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .btn-sidebar-logout {
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            width: 100%; padding: .625rem 1rem;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px; color: rgba(255,255,255,0.75);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: .8125rem; font-weight: 600; cursor: pointer;
            transition: all .2s;
        }

        .btn-sidebar-logout:hover {
            background: rgba(255,255,255,0.12);
            color: #fff; border-color: rgba(255,255,255,0.25);
        }

        /* ── MAIN WRAPPER ─────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── TOP HEADER ───────────────────────────────── */
        .top-header {
            position: sticky; top: 0; z-index: 50;
            height: var(--header-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 2rem;
            gap: 1rem;
            box-shadow: 0 1px 6px rgba(1,41,77,0.06);
        }

        .header-titles { flex: 1; min-width: 0; }

        .header-page-title {
            font-size: 1rem; font-weight: 700; color: var(--navy);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .header-page-sub {
            font-size: .75rem; color: var(--gray); font-weight: 500; margin-top: 1px;
        }

        .header-actions { display: flex; align-items: center; gap: .75rem; flex-shrink: 0; }

        /* ── CONTENT AREA ─────────────────────────────── */
        .content-area { flex: 1; padding: 1.75rem 2rem; }

        /* ── ALERTS ───────────────────────────────────── */
        .alert-box {
            display: flex; align-items: flex-start; gap: .75rem;
            padding: .875rem 1.125rem;
            border-radius: 12px; margin-bottom: 1.25rem;
            border-left: 4px solid;
            font-size: .875rem; font-weight: 500;
        }

        .alert-success {
            background: #f0fdf4;
            border-left-color: #16a34a; color: #15803d;
        }

        .alert-danger {
            background: #fef2f2;
            border-left-color: #dc2626; color: #dc2626;
        }

        /* ── CARDS ────────────────────────────────────── */
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(1,41,77,0.06);
        }

        .card-header {
            padding: 1.125rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-title {
            font-size: .9375rem; font-weight: 700; color: var(--navy);
            display: flex; align-items: center; gap: .5rem; margin: 0;
        }

        .card-title::before {
            content: '';
            display: block; width: 3px; height: 1rem; border-radius: 3px;
            background: linear-gradient(180deg, var(--cyan), var(--cyan2));
        }

        .card-body { padding: 1.5rem; }

        /* ── KPI CARDS ────────────────────────────────── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }

        .kpi-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(1,41,77,0.06);
            display: flex; align-items: flex-start; gap: 1rem;
            transition: transform .25s, box-shadow .25s;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(3,174,198,0.12);
        }

        .kpi-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; flex-shrink: 0;
        }

        .kpi-icon.cyan  { background: rgba(3,174,198,0.12); color: var(--cyan); }
        .kpi-icon.navy  { background: rgba(1,41,77,0.08);   color: var(--navy); }
        .kpi-icon.green { background: rgba(22,163,74,0.10); color: #16a34a; }
        .kpi-icon.amber { background: rgba(217,119,6,0.10); color: #d97706; }
        .kpi-icon.red   { background: rgba(220,38,38,0.10); color: #dc2626; }

        .kpi-label {
            font-size: .75rem; font-weight: 600; color: var(--gray);
            text-transform: uppercase; letter-spacing: .05em; margin-bottom: .25rem;
        }

        .kpi-value {
            font-size: 1.5rem; font-weight: 800; color: var(--navy); line-height: 1;
        }

        .kpi-sub {
            font-size: .75rem; font-weight: 500; color: var(--gray); margin-top: .25rem;
        }

        /* ── TABLES ───────────────────────────────────── */
        .table-wrap {
            overflow-x: auto; border-radius: 12px;
        }

        table.admin-table {
            width: 100%; border-collapse: collapse;
            font-size: .875rem;
        }

        .admin-table thead tr {
            background: #f8fafc;
            border-bottom: 2px solid var(--border);
        }

        .admin-table th {
            padding: .75rem 1rem;
            font-size: .75rem; font-weight: 700; color: var(--gray);
            text-transform: uppercase; letter-spacing: .05em;
            white-space: nowrap; text-align: left;
        }

        .admin-table td {
            padding: .875rem 1rem;
            border-bottom: 1px solid var(--border);
            color: var(--text); vertical-align: middle;
        }

        .admin-table tbody tr:last-child td { border-bottom: none; }

        .admin-table tbody tr:hover td { background: #f8fafc; }

        /* ── BADGES ───────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center;
            padding: .25rem .625rem; border-radius: 999px;
            font-size: .7rem; font-weight: 700; letter-spacing: .02em;
        }

        .badge-cyan    { background: rgba(3,174,198,0.12); color: var(--cyan); }
        .badge-green   { background: rgba(22,163,74,0.10); color: #16a34a; }
        .badge-amber   { background: rgba(217,119,6,0.10); color: #d97706; }
        .badge-red     { background: rgba(220,38,38,0.10); color: #dc2626; }
        .badge-gray    { background: #f1f5f9; color: var(--gray); }

        /* ── BUTTONS ──────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1.125rem; border-radius: 9px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: .8125rem; font-weight: 700; cursor: pointer;
            text-decoration: none; transition: all .2s; border: none;
            white-space: nowrap;
        }

        .btn-cyan {
            background: linear-gradient(135deg, var(--cyan), var(--cyan2));
            color: #fff;
        }

        .btn-cyan:hover {
            color: #fff; transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(3,174,198,0.32);
        }

        .btn-outline-cyan {
            background: transparent;
            border: 1.5px solid var(--cyan); color: var(--cyan);
        }

        .btn-outline-cyan:hover {
            background: var(--cyan); color: #fff;
        }

        .btn-ghost {
            background: #f1f5f9; color: var(--gray);
        }

        .btn-ghost:hover { background: #e2e8f0; color: var(--navy); }

        .btn-danger {
            background: rgba(220,38,38,0.10); color: #dc2626;
        }

        .btn-danger:hover { background: #dc2626; color: #fff; }

        .btn-sm { padding: .375rem .75rem; font-size: .75rem; border-radius: 7px; }

        /* ── FORMS ────────────────────────────────────── */
        .form-label {
            font-size: .8125rem; font-weight: 600; color: var(--navy);
            margin-bottom: .375rem; display: block;
        }

        .form-control, .form-select {
            width: 100%; height: 42px;
            padding: 0 .875rem;
            border: 2px solid var(--border); border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: .875rem; color: var(--navy); background: #f8fafc;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus, .form-select:focus {
            outline: none; border-color: var(--cyan);
            background: #fff; box-shadow: 0 0 0 3px rgba(3,174,198,0.1);
        }

        textarea.form-control { height: auto; padding: .75rem .875rem; }

        /* ── PAGINATION ───────────────────────────────── */
        .pagi-list {
            display: flex; align-items: center; flex-wrap: wrap;
            gap: .375rem; list-style: none; padding: 0; margin: 0;
        }

        .pagi-list li a, .pagi-list li span {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 2.25rem; height: 2.25rem; padding: 0 .75rem;
            border-radius: 9px; font-size: .8125rem; font-weight: 600;
            text-decoration: none !important; border: 1.5px solid var(--border);
            background: #fff; color: var(--navy) !important;
            transition: all .2s;
        }

        .pagi-list li a:hover {
            border-color: var(--cyan); color: var(--cyan) !important;
            background: rgba(3,174,198,0.06);
        }

        .pagi-list li.active span {
            background: linear-gradient(135deg, var(--cyan), var(--cyan2));
            border-color: var(--cyan); color: #fff !important;
            box-shadow: 0 4px 10px rgba(3,174,198,0.3);
        }

        .pagi-list li.disabled span {
            color: #cbd5e0 !important; background: #f8fafc !important;
            border-color: #f1f5f9 !important; cursor: not-allowed;
        }

        /* ── RESPONSIVE ───────────────────────────────── */
        .sidebar-toggle-btn { display: none; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .3s; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .content-area { padding: 1rem; }
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }

            .sidebar-toggle-btn {
                display: flex; flex-direction: column;
                justify-content: center; gap: 5px;
                width: 38px; height: 38px;
                background: #f1f5f9; border: 1.5px solid var(--border);
                border-radius: 9px; cursor: pointer; padding: 0 9px;
                flex-shrink: 0; margin-right: .5rem;
                transition: background .2s;
            }
            .sidebar-toggle-btn:hover { background: var(--border); }
            .sidebar-toggle-btn span {
                display: block; height: 2px; background: var(--navy);
                border-radius: 2px;
                transition: transform .3s, opacity .3s, width .3s;
                width: 100%;
            }
            .sidebar-toggle-btn.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
            .sidebar-toggle-btn.open span:nth-child(2) { opacity: 0; width: 0; }
            .sidebar-toggle-btn.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ── SIDEBAR ──────────────────────────────────────── --}}
    <aside class="sidebar">
        {{-- Brand --}}
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="MuezzaStore">
            <span class="sidebar-brand-text">Muezza<span>Store</span></span>
        </a>

        {{-- User --}}
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="sidebar-user-name">
                {{ auth()->user()->username ?? auth()->user()->email }}
            </div>
        </div>

        {{-- Navigation --}}
        <nav>
            <div class="nav-label">Menu</div>

            <a class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>

            <a class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
               href="{{ route('admin.orders.index') }}">
                <i class="fas fa-receipt"></i> Orders
            </a>

            <a class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
               href="{{ route('admin.products.index') }}">
                <i class="fas fa-box"></i> Products
            </a>

            <a class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="fas fa-users"></i> Users
            </a>
        </nav>

        {{-- Footer --}}
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-sidebar-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN WRAPPER ──────────────────────────────────── --}}
    <div class="main-wrapper">

        {{-- Top Header --}}
        <header class="top-header">
            {{-- Mobile hamburger --}}
            <button id="sidebarToggle" class="sidebar-toggle-btn" aria-label="Buka menu">
                <span></span><span></span><span></span>
            </button>
            <div class="header-titles">
                <div class="header-page-title">@yield('page_title', 'Dashboard')</div>
                <div class="header-page-sub">@yield('page_subtitle')</div>
            </div>
            <div class="header-actions">
                @yield('header_actions')
            </div>
        </header>

        {{-- Content --}}
        <div class="content-area">

            {{-- Alerts --}}
            @if (session('success'))
                <div class="alert-box alert-success">
                    <i class="fas fa-check-circle" style="font-size:1.125rem;margin-top:1px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-box alert-danger">
                    <i class="fas fa-exclamation-circle" style="font-size:1.125rem;margin-top:1px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>

    </div>

    {{-- Mobile sidebar overlay --}}
    <div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:99;backdrop-filter:blur(2px);"></div>

    @stack('scripts')
    <script>
        (function(){
            const sidebar  = document.querySelector('.sidebar');
            const overlay  = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('sidebarToggle');

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.style.display = 'block';
                toggleBtn?.classList.add('open');
            }
            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.style.display = 'none';
                toggleBtn?.classList.remove('open');
            }

            toggleBtn?.addEventListener('click', () => {
                sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
            });
            overlay?.addEventListener('click', closeSidebar);

            // Close on nav link click (mobile)
            sidebar?.querySelectorAll('.nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) closeSidebar();
                });
            });
        })();
    </script>
</body>
</html>
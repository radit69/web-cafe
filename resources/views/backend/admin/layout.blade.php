<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar: #354024;
            --sidebar-dark: #202a10;
            --content-bg: #fff8f3;
            --card-bg: #faecd8;
            --card-inner: #efe0cd;
            --text-main: #211b0f;
            --text-sub: #45483f;
            --accent-pill: #dae8c0;
            --accent-danger: #ba1a1a;
            --accent-success: #889063;
            --secondary: #CFBB99;
            --neutral: #E5D7C4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            display: flex;
            background: var(--content-bg);
        }

        .sidebar {
            width: 260px;
            background: var(--sidebar);
            color: #fff;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 32px 24px 28px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
        }

        .avatar-circle {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: #F7D6B5;
            color: var(--sidebar);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 16px;
        }

        .sidebar-role {
            font-size: 24px;
            font-weight: 700;
        }

        .sidebar-menu {
            padding: 24px 0;
            list-style: none;
            flex: 1;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 26px;
            font-size: 14px;
            color: #fbeeea;
            text-decoration: none;
            gap: 10px;
            transition: background 0.18s ease, padding-left 0.12s ease;
        }

        .sidebar-link span.icon {
            font-size: 16px;
        }

        .sidebar-link.active,
        .sidebar-link:hover {
            background: var(--sidebar-dark);
            padding-left: 32px;
        }

        .main {
            flex: 1;
            background: var(--content-bg);
            display: flex;
            flex-direction: column;
        }

        .main-header {
            padding: 24px 32px 18px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            /* background: #F1C97C; removed to match body */
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #000000; /* Updated to black */
        }

        .main-content {
            padding: 24px 32px 32px;
        }

        .card-row {
            display: flex;
            gap: 20px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 220px;
            background: var(--card-bg);
            border-radius: 18px;
            padding: 18px 20px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-sub);
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--sidebar);
        }

        .content-card {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 18px 20px 20px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead {
            background: var(--card-inner);
        }

        th, td {
            padding: 10px 14px;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.25);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
        }

        .badge-success {
            background: rgba(139, 195, 74, 0.18);
            color: #4b8b1f;
        }

        .badge-danger {
            background: rgba(229, 115, 115, 0.18);
            color: #b93b3b;
        }

        .btn-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 18px;
            border-radius: 999px;
            border: none;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            background: var(--sidebar);
            color: #fff;
            text-decoration: none;
        }

        .btn-pill:hover {
            opacity: 0.95;
        }

        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-bottom: 18px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="avatar-circle"><i class="fa-solid fa-user"></i></div>
            <div class="sidebar-role">Admin</div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-chart-line"></i></span>Dashboard</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-users"></i></span>Manajemen User</a></li>
            <li><a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-user-group"></i></span>Manajemen Customer</a></li>
            <li><a href="{{ route('admin.menus.index') }}" class="sidebar-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-utensils"></i></span>Manajemen Menu</a></li>
            <li><a href="{{ route('admin.inventory') }}" class="sidebar-link {{ request()->routeIs('admin.inventory') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-boxes-stacked"></i></span>Inventori</a></li>
            <li><a href="{{ route('admin.reservations') }}" class="sidebar-link {{ request()->routeIs('admin.reservations') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-calendar-check"></i></span>Daftar Reservasi</a></li>
            <li><a href="{{ route('admin.sales') }}" class="sidebar-link {{ request()->routeIs('admin.sales') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>Laporan Penjualan</a></li>
            <li><a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-gear"></i></span>Pengaturan Sistem</a></li>
            <li>
                <form action="{{ route('select.role') }}" method="GET" style="display: block; width: 100%;">
                    <button type="submit" class="sidebar-link" style="background:none; border:none; cursor:pointer; width:100%; text-align:left; font-family:inherit; font-size: 14px; color: #fbeeea;">
                        <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span> Keluar
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="main">
        <header class="main-header">
            <h1 class="page-title">@yield('page_title')</h1>
        </header>

        <section class="main-content">
            @yield('content')
        </section>
    </main>
    @yield('scripts')
</body>
</html>



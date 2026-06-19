<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <style>
        :root {
            --primary: #354024;
            --sidebar: #354024;
            --sidebar-dark: #202a10;
            --content-bg: #fff8f3;
            --card-bg: #faecd8;
            --card-inner: #efe0cd;
            --text-main: #211b0f;
            --text-sub: #45483f;
            --text-white: #ffffff;
            --accent-pill: #dae8c0;
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
            font-family: 'Poppins', sans-serif;
            display: flex;
            background: var(--content-bg);
            overflow: hidden; /* Prevent body scroll if content scrolls */
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--sidebar);
            color: var(--text-white);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 32px 24px 28px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .avatar-circle {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: #FBEAC3;
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
            color: #f0f0f0;
            text-decoration: none;
            gap: 16px;
            transition: all 0.2s;
            font-weight: 500;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            /* background: rgba(0, 0, 0, 0.1); */
            font-weight: 700;
            color: #fff;
        }
        
        .sidebar-link.active {
            text-shadow: 0 0 1px rgba(255,255,255,0.5);
        }

        .sidebar-link span.icon {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        /* Main Content */
        .main {
            flex: 1;
            background: var(--content-bg);
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
        }

        .main-header {
            padding: 32px 40px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #000000; /* Updated to black */
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 48px;
            border-radius: 999px;
            border: 1px solid #D7C4A3;
            background: rgba(255, 255, 255, 0.2);
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }
        .search-input::placeholder {
            color: #000000ff;
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #555;
            pointer-events: none;
        }

        .content-area {
            padding: 0 40px 40px;
            flex: 1;
        }
        
        /* Category Filters */
        .filter-row {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .filter-btn {
            padding: 10px 24px;
            border-radius: 12px;
            background: transparent;
            border: 1px solid #354024;
            color: #211b0f;
            font-family: inherit;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .filter-btn.active {
            background: #354024;
            color: #fff;
            border-color: #354024;
        }
        
        .filter-btn:hover:not(.active) {
            background: rgba(53, 64, 36, 0.1);
        }

        /* Utility */
        .logout-link {
            margin-top: auto;
            margin-bottom: 40px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="avatar-circle"><i class="fas fa-user"></i></div>
            <div class="sidebar-role">{{ request()->get('as') === 'pelanggan' ? 'Pelanggan' : 'Kasir' }}</div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="{{ route('kasir.menu') }}" class="sidebar-link {{ request()->routeIs('kasir.menu') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-utensils"></i></span> Menu</a></li>
            <li><a href="{{ route('kasir.stock') }}" class="sidebar-link {{ request()->routeIs('kasir.stock') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-boxes-stacked"></i></span> Stok</a></li>
            <li><a href="{{ route('kasir.reservations') }}" class="sidebar-link {{ request()->routeIs('kasir.reservations') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-calendar-check"></i></span> Reservasi</a></li>
            <li><a href="{{ route('kasir.report') }}" class="sidebar-link {{ request()->routeIs('kasir.report') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-file-lines"></i></span> Laporan</a></li>
            <li><a href="{{ route('kasir.order') }}" class="sidebar-link {{ request()->routeIs('kasir.order') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-clipboard-list"></i></span> Pesanan</a></li>
            <li><a href="{{ route('kasir.order_status') }}" class="sidebar-link {{ request()->routeIs('kasir.order_status') ? 'active' : '' }}"><span class="icon"><i class="fa-solid fa-receipt"></i></span> Status Pesanan</a></li>
            <li>
                <form action="{{ route('select.role') }}" method="GET" style="display: block; width: 100%;">
                    <button type="submit" class="sidebar-link" style="background:none; border:none; cursor:pointer; width:100%; text-align:left; font-family:inherit;">
                        <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span> Keluar
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="main">
        <header class="main-header">
            <h1 class="page-title">@yield('page_title')</h1>
            @yield('header_right')
        </header>

        <section class="content-area">
            @yield('content')
        </section>
    </main>
</body>
</html>

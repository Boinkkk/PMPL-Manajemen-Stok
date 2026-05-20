<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Manajemen Stok'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #1f2933;
            --sidebar-active: #c99a2e;
            --surface: #ffffff;
            --body-bg: #f4f6f9;
            --text-strong: #1f2933;
            --border-soft: #e5e7eb;
        }

        body {
            min-height: 100vh;
            background: var(--body-bg);
            color: var(--text-strong);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .app-shell {
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 270px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: #d8dee9;
            position: sticky;
            top: 0;
            align-self: flex-start;
        }

        .sidebar-brand {
            padding: 1.35rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            background: var(--sidebar-active);
            color: #ffffff;
        }

        .sidebar .nav-link {
            color: #d8dee9;
            border-radius: .5rem;
            padding: .72rem .9rem;
            margin-bottom: .25rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            font-weight: 500;
        }

        .sidebar .nav-link i {
            width: 1.25rem;
            text-align: center;
        }

        .sidebar-section-title {
            color: rgba(255, 255, 255, .45);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            margin: 1.25rem .9rem .6rem;
            text-transform: uppercase;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(201, 154, 46, .18);
            color: #ffffff;
        }

        .sidebar .nav-link.active {
            border-left: 4px solid var(--sidebar-active);
            padding-left: calc(.9rem - 4px);
        }

        .main-wrapper {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border-soft);
            min-height: 72px;
        }

        .content-card {
            background: var(--surface);
            border: 1px solid var(--border-soft);
            border-radius: .75rem;
            box-shadow: 0 10px 30px rgba(31, 41, 51, .06);
        }

        .content-header {
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-soft);
        }

        .btn-gold {
            background: var(--sidebar-active);
            border-color: var(--sidebar-active);
            color: #ffffff;
            font-weight: 600;
        }

        .btn-gold:hover {
            background: #b88925;
            border-color: #b88925;
            color: #ffffff;
        }

        .btn-outline-secondary-custom {
            border-color: #d1d5db;
            color: #4b5563;
            background: #ffffff;
        }

        .btn-outline-secondary-custom:hover {
            border-color: var(--sidebar-active);
            color: var(--sidebar-active);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            top: 50%;
            left: .95rem;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .input-icon .form-control {
            padding-left: 2.65rem;
        }

        .search-input,
        .form-control,
        .form-select {
            border-color: #d9dee7;
            border-radius: .55rem;
            min-height: 38px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--sidebar-active);
            box-shadow: 0 0 0 .2rem rgba(201, 154, 46, .12);
        }

        .status-filter {
            max-width: 150px;
        }

        .badge-total {
            background: #fff7df;
            color: #8a6418;
            border: 1px solid #f0d486;
        }

        .table-modern thead th {
            background: #f8fafc;
            color: #4b5563;
            border-bottom: 1px solid var(--border-soft);
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .table-modern tbody td {
            border-color: #edf0f4;
            padding-top: .95rem;
            padding-bottom: .95rem;
        }

        .table-modern tbody tr:hover {
            background: #fbfcfe;
        }

        .detail-list dt {
            color: #6b7280;
            font-size: .85rem;
        }

        .detail-list dd {
            margin-bottom: 1rem;
            font-weight: 600;
        }

        @media (max-width: 991.98px) {
            .app-shell {
                display: block;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
                position: static;
            }

            .sidebar-nav {
                display: flex;
                gap: .5rem;
                overflow-x: auto;
                padding-bottom: .5rem;
            }

            .sidebar .nav-link {
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand d-flex align-items-center gap-3">
                <span class="brand-mark"><i class="fa-solid fa-boxes-stacked"></i></span>
                <div>
                    <div class="fw-bold text-white">Manajemen Stok</div>
                    <small class="text-white-50">Jamu Madura</small>
                </div>
            </div>

            <nav class="p-3 sidebar-nav">
                <div class="sidebar-section-title">Menu Utama</div>
                <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    Dashboard
                </a>

                <div class="sidebar-section-title">Master Data</div>
                <a href="{{ Route::has('produk.index') ? route('produk.index') : '#' }}" class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-box"></i>
                    Data Produk
                </a>
                <a href="{{ Route::has('kategori.index') ? route('kategori.index') : '#' }}" class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i>
                    Kategori
                </a>
                <a href="{{ Route::has('satuan.index') ? route('satuan.index') : '#' }}" class="nav-link {{ request()->routeIs('satuan.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-scale-balanced"></i>
                    Satuan
                </a>

                <div class="sidebar-section-title">Transaksi</div>
                <a href="{{ route('retur.index') }}" class="nav-link {{ request()->routeIs('retur.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-rotate-left"></i>
                    Retur Produk
                </a>
                <a href="{{ Route::has('stok-masuk.index') ? route('stok-masuk.index') : '#' }}" class="nav-link {{ request()->routeIs('stok-masuk.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                    Stok Masuk
                </a>
                <a href="{{ Route::has('stok-keluar.index') ? route('stok-keluar.index') : '#' }}" class="nav-link {{ request()->routeIs('stok-keluar.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-fast"></i>
                    Stok Keluar
                </a>
            </nav>
        </aside>

        <div class="main-wrapper">
            <header class="topbar d-flex align-items-center justify-content-between px-4">
                <div>
                    <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
                    <small class="text-muted">Sistem Manajemen Stok</small>
                </div>

                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary-custom btn-sm">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar
                        </button>
                    </form>
                @endauth
            </header>

            <main class="p-3 p-md-4">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

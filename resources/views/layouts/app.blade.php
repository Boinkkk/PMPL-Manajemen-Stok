<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sistem Informasi Stok dan Distribusi Jamu Madura</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">

    <style>
        :root {
            --brand-brown: #7a3d00;
            --brand-brown-dark: #6b3500;
            --brand-gold: #ffb300;
            --brand-orange: #ee7b00;
            --card-white: #ffffff;
            --text-dark: #111111;
            --text-muted: #6b6b6b;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: #ffffff;
            color: var(--text-dark);
            font-family: 'Inter', 'Poppins', sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 66px;
            left: 0;
            width: 298px;
            min-height: calc(100vh - 66px);
            background: var(--brand-brown-dark);
            padding: 0;
            overflow-y: auto;
            z-index: 1050;
        }

        .sidebar-nav {
            margin-top: 0;
        }

        .sidebar-nav .nav-link {
            color: #ffcb38;
            display: flex;
            align-items: center;
            padding: 0.93rem 2.2rem;
            border-radius: 0;
            margin-bottom: 0;
            font-weight: 500;
            white-space: nowrap;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .sidebar-nav .nav-link.active,
        .sidebar-nav .nav-link:hover {
            color: #ffffff;
            background: var(--brand-orange);
            font-weight: 700;
        }

        .main-content {
            margin-left: 298px;
            min-height: 100vh;
            padding: 66px 0 0;
            background: #ffffff;
        }

        .page-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1060;
            background: var(--brand-brown);
            border: none;
            min-height: 66px;
            box-shadow: none;
            border-radius: 0;
            padding: 0 12px 0 40px;
        }

        .page-navbar .navbar-brand {
            margin: 0;
            color: #ffcb38;
            font-size: 1.25rem;
            font-weight: 800;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .app-logo-mark {
            width: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff3d8;
            font-size: 2rem;
            flex-shrink: 0;
        }

        .page-navbar .btn-outline-secondary {
            border-color: transparent;
            color: #ffcb38;
            background: transparent;
            min-width: 190px;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .page-navbar .btn-outline-secondary:hover,
        .page-navbar .btn-outline-secondary:focus {
            color: #ffcb38;
            background: rgba(255, 255, 255, 0.06);
            border-color: transparent;
        }

        .page-navbar .dropdown-menu {
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(44, 62, 80, 0.08);
        }

        .content-card {
            background: var(--card-white);
            border: 1px solid rgba(92, 51, 23, 0.1);
            border-radius: 0;
            box-shadow: none;
            overflow: hidden;
            animation: fadeInUp .45s ease both;
            max-width: none;
            margin: 0;
        }

        .content-card .card-body {
            padding: 1.75rem;
        }

        .content-header {
            border-left: 4px solid var(--brand-gold);
            padding: 1.35rem 1.5rem;
            background: #fff;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(44, 62, 80, 0.04);
        }

        .search-input {
            border-radius: 50px;
            padding-left: 3.4rem;
            height: 50px;
            border: 1px solid rgba(44, 62, 80, 0.18);
            background: #fff;
        }

        .search-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(212, 160, 23, 0.14);
        }

        .badge-total {
            background: rgba(212, 160, 23, 0.18);
            color: var(--text-dark);
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0 0.75rem;
            min-width: 100%;
        }

        .table-modern thead th {
            border-bottom: none;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.78rem;
            background: transparent;
            padding: 1rem 1rem 0.8rem;
        }

        .table-modern tbody tr {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(44, 62, 80, 0.05);
        }

        .table-modern tbody tr:hover {
            transform: translateY(-1px);
            background: #fff7e3;
        }

        .table-modern td,
        .table-modern th {
            border: none;
            vertical-align: middle;
            padding: 1rem 1rem;
        }

        .form-control,
        .form-select {
            border-radius: 1rem;
            padding: 1rem 1.2rem;
            border: 1px solid rgba(44, 62, 80, 0.18);
            background: #fff;
            box-shadow: inset 0 1px 3px rgba(44, 62, 80, 0.04);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .btn-gold {
            background: linear-gradient(135deg, #d6a81b 0%, #c3941e 100%);
            color: #2c3e50;
            border: none;
            box-shadow: 0 14px 30px rgba(200, 149, 42, 0.2);
            border-radius: 0.9rem;
            padding: 0.85rem 1.35rem;
            font-weight: 700;
        }

        .btn-gold:hover,
        .btn-gold:focus {
            background: linear-gradient(135deg, #c59417 0%, #a57a0d 100%);
            color: #2c3e50;
            box-shadow: 0 16px 36px rgba(200, 149, 42, 0.25);
        }

        .btn-outline-secondary-custom {
            border-color: rgba(44, 62, 80, 0.18);
            color: var(--text-dark);
            background: #fff;
            border-radius: 1rem;
            padding: 0.85rem 1.35rem;
        }

        .btn-outline-secondary-custom:hover {
            background: rgba(44, 62, 80, 0.06);
        }

        .input-icon {
            position: relative;
        }

        .input-icon .fa-search,
        .input-icon .fa-magnifying-glass {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(44, 62, 80, 0.45);
            font-size: 0.95rem;
        }

        .input-icon input {
            padding-left: 3.6rem;
        }

        .alert-modern {
            border-radius: 1rem;
            box-shadow: 0 18px 30px rgba(44, 62, 80, 0.06);
        }

        .modal-content {
            border-radius: 1.2rem;
            overflow: hidden;
        }

        .modal {
            z-index: 2000;
        }

        .modal-backdrop {
            z-index: 1990;
        }

        .modal-header,
        .modal-footer {
            border: none;
        }

        .btn-sm-square {
            width: 38px;
            height: 38px;
            padding: 0;
        }

        .loading-spinner {
            width: 1rem;
            height: 1rem;
            border-width: 0.18rem;
        }

        .fade-in {
            animation: fadeInUp .45s ease both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                top: 66px;
                transform: translateX(-110%);
                transition: transform .3s ease;
                box-shadow: 0 24px 55px rgba(0, 0, 0, 0.18);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 66px 0 0;
            }

            .page-navbar {
                border-radius: 0;
                padding: 0.85rem 1rem;
            }

            .page-navbar .navbar-brand {
                font-size: 0.85rem;
                white-space: normal;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <aside id="appSidebar" class="sidebar">
            <div class="d-flex justify-content-end p-2 d-lg-none">
                <button class="btn btn-outline-light d-lg-none" type="button" id="sidebarClose">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <nav class="nav flex-column sidebar-nav">
                @hasSection('sidebar_nav')
                    @yield('sidebar_nav')
                @else
                    <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}" href="#">
                        Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ \Illuminate\Support\Facades\Route::has('kategori.index') ? route('kategori.index') : '#' }}">
                        Kategori
                    </a>
                    <a class="nav-link {{ request()->routeIs('satuan.*') ? 'active' : '' }}" href="{{ \Illuminate\Support\Facades\Route::has('satuan.index') ? route('satuan.index') : '#' }}">
                        Satuan
                    </a>
                    <a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ \Illuminate\Support\Facades\Route::has('produk.index') ? route('produk.index') : '#' }}">
                        Produk
                    </a>
                    <a class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}">
                        Supplier
                    </a>
                    <a class="nav-link" href="#">Distributor</a>
                    <a class="nav-link" href="#">Stok Masuk</a>
                    <a class="nav-link" href="#">Stok Keluar</a>
                    <a class="nav-link" href="#">Order Distribusi</a>
                    <a class="nav-link" href="#">Komunikasi Supplier</a>
                    <a class="nav-link" href="#">Monitoring</a>
                    <a class="nav-link" href="#">Laporan</a>
                    <a class="nav-link" href="#">Audit Trail</a>
                    <a class="nav-link" href="#">Retur Barang</a>
                @endif
            </nav>
        </aside>

        <div class="flex-grow-1 main-content">
            <nav class="navbar navbar-expand-lg page-navbar px-4 mb-4">
                <div class="container-fluid px-0">
                    <div class="d-flex align-items-center min-w-0">
                        <button class="btn btn-outline-secondary d-lg-none me-3" type="button" id="sidebarToggle">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div class="app-logo-mark me-3">
                            <i class="fa-solid fa-seedling"></i>
                        </div>
                        <div class="navbar-brand mb-0">
                            <h5 class="mb-0">Sistem Informasi Stok dan Distribusi Jamu Madura</h5>
                        </div>
                    </div>

                    <div class="dropdown ms-auto">
                        <button class="btn btn-outline-secondary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @yield('user_greeting', 'Halo, Ivan (Admin)')
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user-gear me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main>
                @if(session('success'))
                    <div class="alert alert-success alert-modern alert-dismissible fade show mx-4" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-modern alert-dismissible fade show mx-4" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('appSidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('show');
                });
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', function () {
                    sidebar.classList.remove('show');
                });
            }

            document.querySelectorAll('.js-loading-form').forEach(function (form) {
                form.addEventListener('submit', function () {
                    const button = form.querySelector('button[type="submit"]');

                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<span class="spinner-border spinner-border-sm loading-spinner me-2" role="status" aria-hidden="true"></span>Memuat...';
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

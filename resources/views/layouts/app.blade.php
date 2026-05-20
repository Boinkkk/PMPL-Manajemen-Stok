<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Sistem Informasi Stok dan Distribusi Jamu Madura</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
    <style>
        :root {
            --brand-brown: #7a3d00;
            --brand-brown-dark: #6b3500;
            --brand-gold: #ffb300;
            --brand-orange: #ee7b00;
            --page-bg: #ffffff;
            --card-white: #ffffff;
            --text-dark: #111111;
            --text-muted: #6b6b6b;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: #faf7f0;
            color: var(--text-dark);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
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
            box-shadow: none;
        }

        .sidebar .brand {
            display: block;
            color: #ffcb38;
            text-decoration: none;
            padding: 12px 28px 18px 36px;
        }

        .sidebar .brand h4 {
            color: #ffcb38;
            font-size: 1.35rem;
            line-height: 1.25;
            letter-spacing: 0;
            text-transform: uppercase;
            font-weight: 800;
        }

        .sidebar .brand small {
            color: #ffcb38;
            font-size: 0.86rem;
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
            transform: none;
            font-weight: 700;
        }

        .main-content {
            margin-left: 298px;
            min-height: 100vh;
            padding: 66px 0 0;
            background: #faf7f0;
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
            min-width: 0;
        }

        .page-navbar .navbar-brand h5 {
            max-width: min(54vw, 720px);
            overflow: hidden;
            text-overflow: ellipsis;
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
            font-size: 1.35rem;
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
            border-radius: 0.9rem;
            box-shadow: 0 16px 36px rgba(63, 36, 18, 0.06);
            overflow: hidden;
            animation: fadeInUp .45s ease both;
            max-width: none;
            margin: 1rem;
        }

        .content-card:hover {
            transform: none;
        }

        .content-card .card-body {
            padding: 1.75rem;
        }

        .content-header {
            border-left: 4px solid var(--brand-gold);
            padding: 1.35rem 1.5rem;
            background: #fffdf8;
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
            min-width: 940px;
        }

        .table-modern.table-product {
            min-width: 1120px;
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
            background: #fffdf8;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(44, 62, 80, 0.05);
        }

        .table-modern tbody tr:hover {
            transform: translateY(-1px);
            background: #fff7e3;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0.85rem;
        }

        .table-modern td,
        .table-modern th {
            border: none;
            vertical-align: middle;
            padding: 1rem 1rem;
            line-height: 1.35;
        }

        .table-modern .text-money,
        .table-modern .stock-badge {
            white-space: nowrap;
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
            background: linear-gradient(135deg, #f0b536 0%, #d99a22 100%);
            color: #2b1a10;
            border: none;
            box-shadow: 0 14px 30px rgba(217, 154, 34, 0.24);
            border-radius: 0.9rem;
            padding: 0.85rem 1.35rem;
            font-weight: 700;
        }

        .btn-gold:hover,
        .btn-gold:focus {
            background: linear-gradient(135deg, #e2a226 0%, #bd7f13 100%);
            color: #2b1a10;
            box-shadow: 0 16px 36px rgba(217, 154, 34, 0.3);
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

        .btn-back {
            color: #fff;
            background: linear-gradient(135deg, #6b3f1d 0%, #3f2412 100%);
            border: none;
            border-radius: 0.9rem;
            padding: 0.85rem 1.35rem;
            box-shadow: 0 12px 24px rgba(63, 36, 18, 0.2);
            font-weight: 700;
        }

        .btn-back:hover,
        .btn-back:focus {
            color: #fff;
            background: linear-gradient(135deg, #7a4a24 0%, #4c2c16 100%);
        }

        .table-action-cell,
        .table-action-heading {
            width: 190px;
            min-width: 190px;
        }

        .table-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: nowrap;
            white-space: nowrap;
            width: max-content;
            margin-inline: auto;
        }

        .table-actions form {
            display: inline-flex;
            margin: 0;
        }

        .table-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 84px;
            min-height: 38px;
            padding: 0.45rem 0.75rem;
            font-weight: 700;
            border-radius: 0.6rem;
            line-height: 1;
            white-space: nowrap;
        }

        .table-actions .btn-outline-primary {
            color: #1f62d0;
            border-color: #8bb5ff;
            background: #f7fbff;
        }

        .table-actions .btn-outline-primary:hover,
        .table-actions .btn-outline-primary:focus {
            color: #fff;
            border-color: #1f62d0;
            background: #1f62d0;
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

            .page-navbar .navbar-brand h5 {
                max-width: 48vw;
                font-size: 0.95rem;
            }

            .page-navbar .btn-outline-secondary {
                min-width: auto;
                font-size: 1rem;
                padding-inline: 0.75rem;
            }

            .table-actions {
                justify-content: center;
            }
        }

        @media (max-width: 575.98px) {
            .content-card {
                margin: 0.75rem;
                border-radius: 0.75rem;
            }

            .content-card.p-4 {
                padding: 1rem !important;
            }

            .content-header {
                padding: 1rem;
                gap: 1rem;
            }

            .content-header .btn,
            .content-header a.btn,
            form .btn {
                width: 100%;
                justify-content: center;
            }

            .search-input {
                height: 46px;
                font-size: 0.92rem;
            }

            .table-modern {
                min-width: 760px;
            }

            .table-modern.table-product {
                min-width: 1040px;
            }

            .table-modern td,
            .table-modern th {
                padding: 0.85rem 0.75rem;
            }

            .app-logo-mark {
                width: 36px;
                font-size: 1.35rem;
                margin-right: 0.6rem !important;
            }

            .page-navbar {
                padding-inline: 0.75rem !important;
            }

            .page-navbar .navbar-brand h5 {
                max-width: 42vw;
                font-size: 0.82rem;
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
                <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                    Kategori
                </a>
                <a class="nav-link {{ request()->routeIs('satuan.*') ? 'active' : '' }}" href="{{ route('satuan.index') }}">
                    Satuan
                </a>
                <a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
                    Produk
                </a>
            </nav>
        </aside>

        <div class="flex-grow-1 main-content">
            <nav class="navbar navbar-expand-lg page-navbar px-4 mb-4">
                <div class="container-fluid px-0">
                    <div class="d-flex align-items-center">
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
                            Halo, Ivan (Admin)
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
                    <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

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
                form.addEventListener('submit', function (event) {
                    const button = form.querySelector('button[type="submit"]');

                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<span class="spinner-border spinner-border-sm loading-spinner me-2" role="status" aria-hidden="true"></span>Memuat...';
                    }
                });
            });

            // Delete modal removed for satuan; per-row inline delete forms are used instead.
        });
    </script>
    @stack('scripts')
</body>
</html>

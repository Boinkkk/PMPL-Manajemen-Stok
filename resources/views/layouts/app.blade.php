<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Kategori')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-VkQtJ1/sP76BMOjQtaWpZprYERnv2MW4NppjHAcELq7bsvDOe7z5Y+ZcRQen5m0J" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.4/css/all.min.css" integrity="sha512-pb5ZiQIxZg6R6dHT1sKdb9uMp6pR/YdC7nZJfu8l1WJ2P1schu7R9XqVVAxBNH5JGvni5iYx3rJkQyLRhS3c0A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --brand-brown: #5D3A1A;
            --brand-gold: #D4A017;
            --page-bg: #F8F9FC;
            --card-white: #FFFFFF;
            --text-dark: #2C3E50;
            --text-muted: #7A8A99;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--page-bg);
            color: var(--text-dark);
            font-family: 'Inter', 'Poppins', sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            min-height: 100vh;
            background: linear-gradient(180deg, #5D3A1A 0%, #3f2a12 100%);
            padding: 2rem 1.6rem;
            overflow-y: auto;
            z-index: 1050;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.17);
        }

        .sidebar .brand {
            color: #fff;
            text-decoration: none;
        }

        .sidebar .brand h4 {
            font-size: 1.45rem;
            letter-spacing: 0.03em;
        }

        .sidebar .brand small {
            color: rgba(255, 255, 255, 0.75);
        }

        .sidebar-nav {
            margin-top: 3rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.95);
            padding: 0.95rem 1.25rem;
            border-radius: 1rem;
            margin-bottom: 0.7rem;
            font-weight: 600;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .sidebar-nav .nav-link.active,
        .sidebar-nav .nav-link:hover {
            color: #2C3E50;
            background: rgba(212, 160, 23, 0.95);
            transform: translateX(4px);
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            padding: 2rem 2rem 3rem 2rem;
            background: #f4f6fb;
        }

        .page-navbar {
            background: #fff;
            border: none;
            min-height: 72px;
            box-shadow: 0 10px 30px rgba(44, 62, 80, 0.08);
            border-radius: 1rem;
            padding: 0.9rem 1.25rem;
        }

        .page-navbar .navbar-brand {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .page-navbar .btn-outline-secondary {
            border-color: rgba(44, 62, 80, 0.14);
            color: #2C3E50;
            background: #fff;
            min-width: 140px;
        }

        .page-navbar .dropdown-menu {
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(44, 62, 80, 0.08);
        }

        .content-card {
            background: var(--card-white);
            border: 1px solid rgba(44, 62, 80, 0.08);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px rgba(44, 62, 80, 0.08);
            overflow: hidden;
            animation: fadeInUp .45s ease both;
            max-width: 1140px;
            margin: 0 auto;
        }

        .content-card:hover {
            transform: translateY(-3px);
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
            color: #2C3E50;
            border: none;
            box-shadow: 0 14px 30px rgba(212, 160, 23, 0.2);
            border-radius: 1rem;
            padding: 0.85rem 1.35rem;
        }

        .btn-gold:hover,
        .btn-gold:focus {
            background: linear-gradient(135deg, #c59417 0%, #a57a0d 100%);
            color: #2C3E50;
            box-shadow: 0 16px 36px rgba(212, 160, 23, 0.25);
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

        .input-icon .fa-search {
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
                transform: translateX(-110%);
                transition: transform .3s ease;
                box-shadow: 0 24px 55px rgba(0, 0, 0, 0.18);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1.5rem 1rem 2rem 1rem;
            }

            .page-navbar {
                border-radius: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <aside id="appSidebar" class="sidebar">
            <div class="d-flex align-items-center justify-content-between">
                <a href="{{ route('kategori.index') }}" class="brand">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white rounded-3 p-2 text-dark shadow-sm">
                            <i class="fa-solid fa-seedling fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">StokApp</h4>
                            <small>Admin Kategori</small>
                        </div>
                    </div>
                </a>
                <button class="btn btn-outline-light d-lg-none" type="button" id="sidebarClose">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <nav class="nav flex-column sidebar-nav mt-5">
                <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                    <i class="fa-solid fa-tags me-3"></i> Daftar Kategori
                </a>
                <a class="nav-link {{ request()->routeIs('satuan.*') ? 'active' : '' }}" href="{{ route('satuan.index') }}">
                    <i class="fa-solid fa-ruler-simple me-3"></i> Daftar Satuan
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
                        <div class="navbar-brand mb-0">
                            <h5 class="mb-0">@yield('title', 'Kategori')</h5>
                        </div>
                    </div>

                    <div class="dropdown ms-auto">
                        <button class="btn btn-outline-secondary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user fa-fw me-2"></i> Admin
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-8oQ/9Ii+1B6kR+c2B4d+2N9tYHJv7VQdOl4eoTC3M1zL3J8kth4wO5RYIqlJcm3d" crossorigin="anonymous"></script>
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

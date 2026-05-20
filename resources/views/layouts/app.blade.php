<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Manajemen Stok Jamu Madura')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-brown: #7a3d00;
            --brand-gold: #ffb300;
            --brand-orange: #ee7b00;
            --soft-bg: #f7f3ee;
        }

        body {
            min-height: 100vh;
            background: var(--soft-bg);
            color: #2f241c;
        }

        .app-shell {
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 260px;
            background: var(--brand-brown);
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar .brand {
            min-height: 72px;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 1rem 1.25rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255, 255, 255, .15);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .86);
            border-radius: .5rem;
            margin: .15rem .75rem;
            padding: .75rem 1rem;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            color: #2f241c;
            background: var(--brand-gold);
        }

        .main-content {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            min-height: 72px;
            background: #fff;
            border-bottom: 1px solid #eadfd2;
        }

        .content-wrap {
            padding: 1.5rem;
        }

        .card-soft {
            background: #fff;
            border: 1px solid #efe2d2;
            border-radius: .85rem;
            box-shadow: 0 12px 30px rgba(122, 61, 0, .08);
        }

        .btn-gold {
            --bs-btn-bg: var(--brand-gold);
            --bs-btn-border-color: var(--brand-gold);
            --bs-btn-color: #2f241c;
            --bs-btn-hover-bg: #e8a200;
            --bs-btn-hover-border-color: #e8a200;
            --bs-btn-hover-color: #2f241c;
            font-weight: 600;
        }

        .bg-brand-brown {
            background: var(--brand-brown);
        }

        @media (max-width: 991.98px) {
            .app-shell {
                display: block;
            }

            .sidebar {
                width: 100%;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <i class="fa-solid fa-mortar-pestle"></i>
                <span>Jamu Madura</span>
            </div>
            <nav class="nav flex-column py-3">
                <a href="{{ route('audit.index') }}" class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Audit Trail
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="topbar d-flex align-items-center justify-content-between px-4">
                <div>
                    <div class="fw-semibold">@yield('page-title', 'Dashboard')</div>
                    <small class="text-muted">Sistem Informasi Manajemen Stok dan Distribusi</small>
                </div>
            </header>

            <div class="content-wrap">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

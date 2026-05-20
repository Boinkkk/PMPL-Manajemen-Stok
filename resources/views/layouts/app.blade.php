<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manajemen Stok Jamu Madura')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-brown: #7a3d00;
            --brand-gold: #ffb300;
            --brand-orange: #ee7b00;
            --surface-soft: #fff8ee;
        }

        body {
            background: var(--surface-soft);
            min-height: 100vh;
        }

        .app-shell {
            min-height: 100vh;
        }

        .sidebar {
            background: var(--brand-brown);
            color: #fff;
            min-height: 100vh;
            width: 260px;
        }

        .sidebar .brand {
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            color: var(--brand-gold);
            font-weight: 800;
            padding: 1.25rem;
        }

        .sidebar .nav-link {
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.82);
            margin: 0.15rem 0.75rem;
            padding: 0.75rem 1rem;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: rgba(255, 179, 0, 0.18);
            color: #fff;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid rgba(122, 61, 0, 0.1);
        }

        .content-area {
            flex: 1;
            min-width: 0;
        }

        .main-content {
            padding: 1.5rem;
        }

        @media (max-width: 991.98px) {
            .app-shell {
                flex-direction: column;
            }

            .sidebar {
                min-height: auto;
                width: 100%;
            }

            .sidebar .nav {
                flex-direction: row;
                overflow-x: auto;
                padding-bottom: 0.75rem;
            }

            .sidebar .nav-link {
                white-space: nowrap;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-shell d-flex">
        <aside class="sidebar">
            <div class="brand">
                <i class="fa-solid fa-mortar-pestle me-2"></i>
                Jamu Madura
            </div>
            <nav class="nav flex-column py-3">
                <a class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>
                    Audit Trail
                </a>
            </nav>
        </aside>

        <div class="content-area">
            <nav class="navbar topbar px-4">
                <span class="navbar-brand mb-0 h1 text-dark">Sistem Informasi Manajemen Stok</span>
            </nav>

            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

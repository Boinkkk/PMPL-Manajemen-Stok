@props(['title' => 'Manajemen Stok Jamu Madura'])

@php
    $user = auth()->user();
    $isAdmin = $user?->hasAnyRole(['Administrator']) === true;
    $isStaff = $user?->hasAnyRole(['Staf Gudang']) === true;
    $isManager = $user?->hasAnyRole(['Manajer']) === true;

    $linkClass = 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition hover:bg-jamu-primary hover:text-white';
    $activeClass = 'bg-jamu-secondary text-jamu-primary-dark shadow-sm';
    $inactiveClass = 'text-jamu-secondary-light';
@endphp

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Manajemen Stok Jamu Madura' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="min-h-screen bg-jamu-bg font-sans text-jamu-text">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
            <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/40 lg:hidden"></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-jamu-primary-dark bg-jamu-primary text-white transition-transform duration-200 print:hidden lg:sticky lg:top-0 lg:h-screen lg:w-auto lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            >
                <div class="flex items-start justify-between gap-3 border-b border-white/10 px-5 py-5">
                    <a href="{{ route('dashboard') }}" class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-jamu-secondary-light">Sistem Informasi</p>
                        <h1 class="mt-1 text-lg font-semibold leading-snug text-white">Manajemen Stok & Distribusi Jamu Madura</h1>
                    </a>
                    <button type="button" @click="sidebarOpen = false" class="rounded-md p-2 text-jamu-secondary-light hover:bg-jamu-primary-dark lg:hidden" aria-label="Tutup menu">
                        <span class="text-xl leading-none">&times;</span>
                    </button>
                </div>

                <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-5">
                    <div class="space-y-1">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wide text-jamu-secondary-light/80">Utama</p>
                        <a href="{{ route('dashboard') }}" class="{{ $linkClass }} {{ request()->routeIs('dashboard*') ? $activeClass : $inactiveClass }}">
                            <span class="w-5 text-center">D</span> Dashboard
                        </a>
                        <a href="{{ route('monitoring.index') }}" class="{{ $linkClass }} {{ request()->routeIs('monitoring*') || request()->routeIs('notifikasi*') ? $activeClass : $inactiveClass }}">
                            <span class="w-5 text-center">M</span> Monitoring
                        </a>
                        @if ($isAdmin || $isManager)
                            <a href="{{ route('laporan.index') }}" class="{{ $linkClass }} {{ request()->routeIs('laporan*') ? $activeClass : $inactiveClass }}">
                                <span class="w-5 text-center">L</span> Laporan
                            </a>
                        @elseif ($isStaff)
                            <a href="{{ route('laporan.show', 'stok-kedaluwarsa') }}" class="{{ $linkClass }} {{ request()->routeIs('laporan*') ? $activeClass : $inactiveClass }}">
                                <span class="w-5 text-center">L</span> Kedaluwarsa
                            </a>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wide text-jamu-secondary-light/80">Operasional</p>
                        <a href="{{ route('order-distribusi.index') }}" class="{{ $linkClass }} {{ request()->routeIs('order-distribusi*') ? $activeClass : $inactiveClass }}">
                            <span class="w-5 text-center">O</span> Order Distribusi
                        </a>
                        <a href="{{ route('stok-masuk.index') }}" class="{{ $linkClass }} {{ request()->routeIs('stok-masuk*') ? $activeClass : $inactiveClass }}">
                            <span class="w-5 text-center">SM</span> Stok Masuk
                        </a>
                        <a href="{{ route('stok-keluar.index') }}" class="{{ $linkClass }} {{ request()->routeIs('stok-keluar*') ? $activeClass : $inactiveClass }}">
                            <span class="w-5 text-center">SK</span> Stok Keluar
                        </a>
                    </div>

                    <div class="space-y-1">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wide text-jamu-secondary-light/80">Master Data</p>
                        <a href="{{ route('produk.index') }}" class="{{ $linkClass }} {{ request()->routeIs('produk*') || request()->routeIs('kategori*') || request()->routeIs('satuan*') || request()->routeIs('batch*') ? $activeClass : $inactiveClass }}">
                            <span class="w-5 text-center">P</span> Data Produk
                        </a>
                        @if ($isAdmin || $isStaff)
                            <a href="{{ route('supplier.index') }}" class="{{ $linkClass }} {{ request()->routeIs('supplier*') ? $activeClass : $inactiveClass }}">
                                <span class="w-5 text-center">S</span> Supplier
                            </a>
                            <a href="{{ route('distributor.index') }}" class="{{ $linkClass }} {{ request()->routeIs('distributor*') ? $activeClass : $inactiveClass }}">
                                <span class="w-5 text-center">D</span> Distributor
                            </a>
                        @endif
                        @if ($isAdmin)
                            <a href="{{ route('pengguna.index') }}" class="{{ $linkClass }} {{ request()->routeIs('pengguna*') ? $activeClass : $inactiveClass }}">
                                <span class="w-5 text-center">U</span> Pengguna
                            </a>
                        @endif
                    </div>

                    @if ($isAdmin || $isManager || $isStaff)
                        <div class="space-y-1">
                            <p class="px-3 text-xs font-semibold uppercase tracking-wide text-jamu-secondary-light/80">Sistem</p>
                            <a href="{{ route('audit.index') }}" class="{{ $linkClass }} {{ request()->routeIs('audit*') ? $activeClass : $inactiveClass }}">
                                <span class="w-5 text-center">A</span> Audit Trail
                            </a>
                        </div>
                    @endif
                </nav>
            </aside>

            <div class="min-w-0">
                <header class="sticky top-0 z-30 border-b border-jamu-border bg-jamu-surface/95 backdrop-blur print:hidden">
                    <div class="flex min-h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                        <div class="flex min-w-0 items-center gap-3">
                            <button type="button" @click="sidebarOpen = true" class="rounded-md border border-jamu-border bg-white px-3 py-2 text-sm font-semibold text-jamu-primary lg:hidden" aria-label="Buka menu">
                                Menu
                            </button>
                            <div class="min-w-0">
                                <p class="truncate text-sm text-jamu-muted">{{ $title }}</p>
                                <p class="truncate text-lg font-semibold">Sistem Informasi Jamu Madura</p>
                            </div>
                        </div>

                        @auth
                            <div class="flex items-center gap-2">
                                <div class="relative" x-data="notificationBell()" x-init="init()">
                                    <button type="button" @click="open = !open" class="relative rounded-md border border-jamu-border bg-white px-3 py-2 text-sm font-semibold text-jamu-primary hover:bg-jamu-bg" aria-label="Notifikasi">
                                        Notifikasi
                                        <span x-cloak x-show="count > 0" x-text="count > 99 ? '99+' : count" class="absolute -right-1 -top-2 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-bold text-white"></span>
                                    </button>

                                    <div x-cloak x-show="open" @click.outside="open = false" class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-md border border-jamu-border bg-jamu-surface text-jamu-text shadow-xl">
                                        <div class="flex items-center justify-between border-b border-jamu-border px-4 py-3">
                                            <p class="font-semibold">Notifikasi</p>
                                            <button type="button" @click="markAll()" class="text-xs font-semibold text-jamu-primary hover:underline">Tandai Semua Dibaca</button>
                                        </div>
                                        <div class="max-h-80 overflow-y-auto">
                                            <template x-if="items.length === 0">
                                                <p class="px-4 py-6 text-center text-sm text-jamu-muted">Tidak ada notifikasi baru.</p>
                                            </template>
                                            <template x-for="item in items" :key="item.id_notifikasi">
                                                <a href="{{ route('notifikasi.index') }}" class="block border-b border-jamu-border px-4 py-3 hover:bg-jamu-bg">
                                                    <div class="flex gap-3">
                                                        <span class="mt-1 h-2 w-2 rounded-full bg-red-600"></span>
                                                        <div class="min-w-0">
                                                            <p class="truncate text-sm font-semibold" x-text="item.pesan"></p>
                                                            <p class="text-xs text-jamu-muted" x-text="`${item.produk ?? '-'} - ${item.waktu}`"></p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                        <a href="{{ route('notifikasi.index') }}" class="block bg-jamu-bg px-4 py-3 text-center text-sm font-semibold text-jamu-primary hover:underline">Lihat Semua Notifikasi</a>
                                    </div>
                                </div>

                                <div class="hidden rounded-md bg-jamu-bg px-3 py-2 text-sm font-semibold text-jamu-primary sm:block">
                                    {{ $user->nama_lengkap ?? $user->name }}
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="rounded-md bg-jamu-secondary px-3 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                </header>

                <main class="flex flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
                    <x-alert />
                    {{ $slot }}
                </main>
            </div>
        </div>

        @auth
            <script>
                function notificationBell() {
                    return {
                        open: false,
                        count: 0,
                        items: [],
                        init() {
                            this.refresh();
                            setInterval(() => this.refresh(), 60000);
                        },
                        async refresh() {
                            const response = await fetch('{{ route('notifikasi.preview') }}', { headers: { 'Accept': 'application/json' } });
                            const json = await response.json();
                            this.count = json.data?.count ?? 0;
                            this.items = json.data?.items ?? [];
                        },
                        async markAll() {
                            await fetch('{{ route('notifikasi.mark-all-read') }}', {
                                method: 'PATCH',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                            });
                            await this.refresh();
                        },
                    };
                }
            </script>
        @endauth
        @stack('scripts')
    </body>
</html>

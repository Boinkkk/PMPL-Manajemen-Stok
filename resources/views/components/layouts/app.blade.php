@props(['title' => 'Manajemen Stok Jamu Madura'])

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Manajemen Stok Jamu Madura' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="min-h-screen bg-jamu-bg font-sans text-jamu-text">
        <div class="min-h-screen">
            <header class="border-b border-jamu-primary-dark bg-jamu-primary text-white print:hidden">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <div>
                        <p class="text-sm text-jamu-secondary-light">Sistem Informasi</p>
                        <h1 class="text-xl font-semibold">Manajemen Stok & Distribusi Jamu Madura</h1>
                    </div>

                    <nav class="flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Dashboard</a>
                        <a href="{{ route('monitoring.index') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Monitoring</a>
                        @if (auth()->user()?->hasAnyRole(['Administrator', 'Manajer']))
                            <a href="{{ route('laporan.index') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Laporan</a>
                        @elseif (auth()->user()?->hasAnyRole(['Staf Gudang']))
                            <a href="{{ route('laporan.show', 'stok-kedaluwarsa') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Laporan Kedaluwarsa</a>
                        @endif
                        <a href="{{ route('order-distribusi.index') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Order Distribusi</a>
                        <a href="{{ route('stok-masuk.index') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Stok Masuk</a>
                        <a href="{{ route('stok-keluar.index') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Stok Keluar</a>
                        <a href="{{ route('produk.index') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Data Produk</a>

                        @auth
                            <div class="relative" x-data="notificationBell()" x-init="init()">
                                <button type="button" @click="open = !open" class="relative rounded-md px-3 py-2 hover:bg-jamu-primary-dark" aria-label="Notifikasi">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5" />
                                        <path d="M10 21h4" />
                                    </svg>
                                    <span x-show="count > 0" x-text="count > 99 ? '99+' : count" class="absolute -right-1 -top-1 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-bold text-white"></span>
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
                            @if (auth()->user()?->isAdministrator())
                                <a href="{{ route('pengguna.index') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Pengguna</a>
                            @endif
                            <span class="rounded-md bg-jamu-primary-dark px-3 py-2">{{ auth()->user()->nama_lengkap ?? auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-md bg-jamu-secondary px-3 py-2 font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                                    Logout
                                </button>
                            </form>
                        @endauth
                    </nav>
                </div>
            </header>

            <main class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
                <x-alert />
                {{ $slot }}
            </main>
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
    </body>
</html>

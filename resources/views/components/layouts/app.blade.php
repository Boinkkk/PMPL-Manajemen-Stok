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
            <header class="border-b border-jamu-primary-dark bg-jamu-primary text-white">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <div>
                        <p class="text-sm text-jamu-secondary-light">Sistem Informasi</p>
                        <h1 class="text-xl font-semibold">Manajemen Stok & Distribusi Jamu Madura</h1>
                    </div>

                    <nav class="flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Dashboard</a>
                        <a href="{{ route('stok-masuk.index') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Stok Masuk</a>
                        <a href="{{ route('stok-keluar.index') }}" class="rounded-md px-3 py-2 hover:bg-jamu-primary-dark">Stok Keluar</a>
                        @auth
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
    </body>
</html>

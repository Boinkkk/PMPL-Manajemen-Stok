<x-layouts.app :title="$title">
    <section class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <div class="rounded-md border border-jamu-border bg-jamu-surface p-6">
            <p class="text-sm font-semibold text-jamu-secondary">Dashboard</p>
            <h2 class="mt-1 text-2xl font-bold text-jamu-primary">{{ $title }}</h2>
            <p class="mt-3 text-jamu-muted">{{ $description }}</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('stok-masuk.index') }}" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Lihat Stok Masuk</a>
                <a href="{{ route('stok-keluar.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm font-semibold hover:bg-jamu-bg">Lihat Stok Keluar</a>
                @if (auth()->user()?->isAdministrator())
                    <a href="{{ route('pengguna.index') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Kelola Pengguna</a>
                @endif
            </div>
        </div>

        <aside class="rounded-md border border-jamu-border bg-jamu-surface p-5">
            <p class="text-sm text-jamu-muted">Login sebagai</p>
            <h3 class="mt-1 font-semibold">{{ auth()->user()?->nama_lengkap }}</h3>
            <div class="mt-3">
                <x-badge :status="auth()->user()?->role?->nama_role" />
            </div>
        </aside>
    </section>
</x-layouts.app>

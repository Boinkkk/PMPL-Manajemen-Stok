<x-layouts.app title="Riwayat Stok Keluar">
    <section class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Riwayat Stok Keluar</h2>
                <p class="text-sm text-jamu-muted">Daftar transaksi distribusi stok keluar.</p>
            </div>

            @if (auth()->user()?->canManageStock())
                <a href="{{ route('stok-keluar.create') }}" class="inline-flex items-center justify-center rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                    Tambah Stok Keluar
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('stok-keluar.index') }}" class="grid gap-3 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-5">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nomor/distributor" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">

            <select name="id_distributor" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <option value="">Semua distributor</option>
                @foreach ($distributors as $distributor)
                    <option value="{{ $distributor->id_distributor }}" @selected(($filters['id_distributor'] ?? '') == $distributor->id_distributor)>
                        {{ $distributor->nama_distributor }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
            <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">

            <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">
                Terapkan Filter
            </button>
        </form>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">Nomor</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Distributor</th>
                    <th class="px-4 py-3">Order</th>
                    <th class="px-4 py-3">Dicatat Oleh</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse ($stokKeluar as $transaksi)
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3 font-medium">{{ $transaksi->nomor_transaksi }}</td>
                        <td class="px-4 py-3">{{ $transaksi->tanggal_keluar_formatted }}</td>
                        <td class="px-4 py-3">{{ $transaksi->distributor?->nama_distributor ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $transaksi->orderDistribusi?->nomor_order ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $transaksi->pengguna?->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('stok-keluar.show', $transaksi) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Detail</a>
                                @if (auth()->user()?->isAdministrator())
                                    <form method="POST" action="{{ route('stok-keluar.destroy', $transaksi) }}" onsubmit="return confirm('Arsipkan transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-jamu-muted">Belum ada transaksi stok keluar.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $stokKeluar->links() }}
    </section>
</x-layouts.app>

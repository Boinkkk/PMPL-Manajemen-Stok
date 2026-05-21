<x-layouts.app title="Data Distributor">
    <section class="flex flex-col gap-5" x-data="{ deleteModal: false, deleteAction: '', deleteName: '' }">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Data Distributor</h2>
                <p class="text-sm text-jamu-muted">Kelola distributor dan pantau nilai distribusi stok keluar.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if (auth()->user()?->isAdministrator())
                    <a href="{{ route('distributor.export', request()->query()) }}" class="rounded-md bg-jamu-green px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Ekspor Excel</a>
                @endif
                <a href="{{ route('distributor.create') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Tambah Distributor</a>
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-4">
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Distributor</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($summary['total_distributor'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-green-200 bg-green-50 p-4">
                <p class="text-sm text-green-800">Distributor Aktif</p>
                <p class="mt-1 text-2xl font-semibold text-green-900">{{ number_format($summary['distributor_aktif'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-yellow-200 bg-yellow-50 p-4">
                <p class="text-sm text-yellow-800">Distributor Tidak Aktif</p>
                <p class="mt-1 text-2xl font-semibold text-yellow-900">{{ number_format($summary['distributor_tidak_aktif'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Nilai Distribusi Bulan Ini</p>
                <p class="mt-1 text-2xl font-semibold">{{ $service->rupiah($summary['total_nilai_bulan_ini']) }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route('distributor.index') }}" class="grid gap-3 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-[1fr_220px_auto_auto]">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, kode, kontak, email, telepon" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
            <select name="sort" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <option value="nama_asc" @selected(($filters['sort'] ?? 'nama_asc') === 'nama_asc')>Nama A-Z</option>
                <option value="nama_desc" @selected(($filters['sort'] ?? '') === 'nama_desc')>Nama Z-A</option>
                <option value="transaksi_desc" @selected(($filters['sort'] ?? '') === 'transaksi_desc')>Transaksi terbanyak</option>
                <option value="nilai_desc" @selected(($filters['sort'] ?? '') === 'nilai_desc')>Nilai distribusi terbesar</option>
                <option value="terbaru" @selected(($filters['sort'] ?? '') === 'terbaru')>Terbaru ditambahkan</option>
            </select>
            <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Terapkan</button>
            <a href="{{ route('distributor.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Reset</a>
        </form>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Distributor</th>
                    <th class="px-4 py-3">Kontak Person</th>
                    <th class="px-4 py-3">Telepon</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3 text-right">Transaksi</th>
                    <th class="px-4 py-3 text-right">Nilai Distribusi</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse ($distributors as $distributor)
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3 font-medium">{{ $distributor->kode_distributor }}</td>
                        <td class="px-4 py-3">{{ $distributor->nama_distributor }}</td>
                        <td class="px-4 py-3">{{ $distributor->kontak_person ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $distributor->telepon ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $distributor->email ?: '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format((int) $distributor->total_transaksi, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">{{ $service->rupiah($distributor->total_nilai_distribusi) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('distributor.show', $distributor) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Detail</a>
                                <a href="{{ route('distributor.edit', $distributor) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Edit</a>
                                @if (auth()->user()?->isAdministrator())
                                    @if ((int) $distributor->total_transaksi > 0)
                                        <button type="button" disabled title="Distributor tidak dapat dihapus karena memiliki riwayat transaksi. Hubungi Administrator jika diperlukan." class="cursor-not-allowed rounded-md border border-red-100 px-3 py-1.5 text-sm text-red-300">Hapus</button>
                                    @else
                                        <button
                                            type="button"
                                            class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50"
                                            @click="deleteModal = true; deleteAction = '{{ route('distributor.destroy', $distributor) }}'; deleteName = '{{ addslashes($distributor->nama_distributor) }}'"
                                        >Hapus</button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-jamu-muted">Tidak ada distributor yang sesuai dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $distributors->links() }}

        <div x-cloak x-show="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="deleteModal = false" class="w-full max-w-md rounded-md border border-jamu-border bg-jamu-surface p-5 shadow-xl">
                <h3 class="text-lg font-semibold">Hapus Distributor</h3>
                <p class="mt-2 text-sm text-jamu-muted">Ketik nama distributor untuk memastikan data yang dihapus benar.</p>
                <form method="POST" :action="deleteAction" x-data="{ confirmation: '' }" class="mt-4 flex flex-col gap-3">
                    @csrf
                    @method('DELETE')
                    <div class="rounded-md bg-jamu-bg px-3 py-2 text-sm font-semibold" x-text="deleteName"></div>
                    <input type="text" x-model="confirmation" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm" placeholder="Nama distributor">
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="deleteModal = false" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-bg">Batal</button>
                        <button type="submit" :disabled="confirmation !== deleteName" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:bg-red-200">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>

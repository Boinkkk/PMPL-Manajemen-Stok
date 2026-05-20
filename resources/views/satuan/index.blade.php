<x-layouts.app title="Data Satuan">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Data Satuan</h2>
                <p class="text-sm text-jamu-muted">Kelola daftar satuan produk untuk transaksi dan laporan stok.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('produk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali ke Produk</a>
                <a href="{{ route('satuan.create') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Tambah Satuan</a>
            </div>
        </div>

        <x-alert />

        <div class="grid gap-3 md:grid-cols-[1fr_auto]">
            <form action="{{ route('satuan.index') }}" method="GET" class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <div class="flex flex-col gap-3 md:flex-row">
                    <input type="text" name="search" class="min-w-0 flex-1 rounded-md border-jamu-border bg-white px-3 py-2 text-sm" placeholder="Cari nama satuan atau singkatan..." value="{{ old('search', $search) }}">
                    <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Cari</button>
                    <a href="{{ route('satuan.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Reset</a>
                </div>
            </form>

            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Satuan</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($satuans->total(), 0, ',', '.') }}</p>
            </div>
        </div>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Nama Satuan</th>
                    <th class="px-4 py-3">Singkatan</th>
                    <th class="px-4 py-3">Tanggal Dibuat</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse($satuans as $index => $satuan)
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3">{{ $satuans->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium">{{ $satuan->nama_satuan }}</td>
                        <td class="px-4 py-3 text-jamu-muted">{{ $satuan->singkatan }}</td>
                        <td class="px-4 py-3 text-jamu-muted">{{ $satuan->created_at?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('satuan.edit', $satuan) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Edit</a>
                                <form action="{{ route('satuan.destroy', $satuan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus satuan {{ $satuan->nama_satuan }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-jamu-muted">Tidak ada satuan yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $satuans->links() }}
    </section>
</x-layouts.app>

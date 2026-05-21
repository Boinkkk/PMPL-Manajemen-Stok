<x-layouts.app title="Data Produk">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Data Produk</h2>
                <p class="text-sm text-jamu-muted">Kelola produk, kategori, satuan, dan batch dalam satu modul.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('satuan.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kelola Satuan</a>
                <a href="{{ route('batch.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kelola Batch</a>
                <a href="{{ route('kategori.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kelola Kategori</a>
                <a href="{{ route('produk.create') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Tambah Produk</a>
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-4">
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Produk</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($totalProduk, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Stok</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($totalStok, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-yellow-200 bg-yellow-50 p-4">
                <p class="text-sm text-yellow-800">Produk Menipis</p>
                <p class="mt-1 text-2xl font-semibold text-yellow-900">{{ number_format($produkMenipis, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-red-200 bg-red-50 p-4">
                <p class="text-sm text-red-800">Produk Habis</p>
                <p class="mt-1 text-2xl font-semibold text-red-900">{{ number_format($produkHabis, 0, ',', '.') }}</p>
            </div>
        </div>

        <form action="{{ route('produk.index') }}" method="GET" class="grid gap-3 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-[1fr_240px_auto_auto]">
            <input type="text" name="search" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm" placeholder="Cari nama produk atau kode produk..." value="{{ old('search', $search) }}">
            <select name="kategori" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <option value="">Semua kategori</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id_kategori }}" @selected((string) $kategoriFilter === (string) $kategori->id_kategori)>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Cari</button>
            <a href="{{ route('produk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Reset</a>
        </form>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Kode Produk</th>
                    <th class="px-4 py-3">Nama Produk</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Satuan</th>
                    <th class="px-4 py-3 text-right">Harga</th>
                    <th class="px-4 py-3">Status Batch</th>
                    <th class="px-4 py-3">Stok</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse($produks as $index => $produk)
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3">{{ $produks->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium">{{ $produk->kode_produk }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $produk->nama_produk }}</div>
                            <div class="max-w-xs truncate text-xs text-jamu-muted">{{ $produk->deskripsi ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $produk->kategori?->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $produk->satuan?->nama_satuan ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ $produk->formatted_harga }}</td>
                        <td class="px-4 py-3">
                            @php($batch = $produk->batches->first())

                            @if(!$batch)
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">Belum Ada Batch</span>
                            @elseif($batch->tanggal_expired->isPast())
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">Kedaluwarsa</span>
                            @elseif($batch->tanggal_expired->lte(now()->addDays(30)))
                                <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">Segera Expired</span>
                            @else
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($produk->stok_terkini == 0)
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">Habis</span>
                            @elseif($produk->stok_terkini <= $produk->stok_minimum)
                                <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">Menipis ({{ $produk->stok_terkini }})</span>
                            @else
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Tersedia ({{ $produk->stok_terkini }})</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('produk.show', $produk->id_produk) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Detail</a>
                                <a href="{{ route('produk.edit', $produk->id_produk) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Edit</a>
                                <form action="{{ route('produk.destroy', $produk->id_produk) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $produk->nama_produk }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-jamu-muted">Tidak ada produk yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $produks->links() }}
    </section>
</x-layouts.app>

<x-layouts.app title="Daftar Kategori">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Daftar Kategori</h2>
                <p class="text-sm text-jamu-muted">Kelola kategori produk agar data stok lebih mudah dikelompokkan.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('produk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali ke Produk</a>
                <a href="{{ route('kategori.create') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Tambah Kategori</a>
            </div>
        </div>

        <x-alert />

        <div class="grid gap-3 md:grid-cols-[1fr_auto]">
            <form action="{{ route('kategori.index') }}" method="GET" class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <div class="flex flex-col gap-3 md:flex-row">
                    <input type="text" name="search" class="min-w-0 flex-1 rounded-md border-jamu-border bg-white px-3 py-2 text-sm" placeholder="Cari kategori..." value="{{ old('search', $search) }}">
                    <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Cari</button>
                    <a href="{{ route('kategori.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Reset</a>
                </div>
            </form>

            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Kategori</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($kategoris->total(), 0, ',', '.') }}</p>
            </div>
        </div>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Nama Kategori</th>
                    <th class="px-4 py-3">Deskripsi</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse($kategoris as $index => $kategori)
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3">{{ $kategoris->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium">{{ $kategori->nama_kategori }}</td>
                        <td class="px-4 py-3 text-jamu-muted">{{ $kategori->deskripsi ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('kategori.edit', $kategori) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Edit</a>
                                <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $kategori->nama_kategori }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-jamu-muted">Tidak ada kategori yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $kategoris->links() }}
    </section>
</x-layouts.app>

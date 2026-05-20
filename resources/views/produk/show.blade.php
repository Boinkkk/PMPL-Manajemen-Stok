<x-layouts.app title="Detail Produk">
    <section class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Detail Produk</h2>
                <p class="text-sm text-jamu-muted">{{ $produk->kode_produk }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('produk.edit', $produk->id_produk) }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Edit</a>
                <a href="{{ route('produk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
            </div>
        </div>

        <div class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-5 md:grid-cols-3">
            <div class="md:col-span-3">
                <p class="text-xs text-jamu-muted">Nama Produk</p>
                <p class="text-xl font-semibold">{{ $produk->nama_produk }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Kategori</p>
                <p class="font-medium">{{ $produk->kategori?->nama_kategori ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Satuan</p>
                <p class="font-medium">{{ $produk->satuan?->nama_satuan ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Harga</p>
                <p class="font-medium">{{ $produk->formatted_harga }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Stok Terkini</p>
                <p class="font-medium">{{ number_format($produk->stok_terkini, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Stok Minimum</p>
                <p class="font-medium">{{ number_format($produk->stok_minimum, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Status</p>
                @if($produk->stok_terkini == 0)
                    <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">Habis</span>
                @elseif($produk->stok_terkini <= $produk->stok_minimum)
                    <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">Menipis</span>
                @else
                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Normal</span>
                @endif
            </div>
            <div class="md:col-span-3">
                <p class="text-xs text-jamu-muted">Deskripsi</p>
                <div class="mt-1 rounded-md bg-jamu-bg p-4 text-sm">
                    {!! nl2br(e($produk->deskripsi ?: '-')) !!}
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>

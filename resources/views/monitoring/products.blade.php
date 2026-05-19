<x-layouts.app title="Daftar Produk Monitoring">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Daftar Produk</h2>
                <p class="text-sm text-jamu-muted">Ringkasan produk untuk monitoring stok.</p>
            </div>
            <a href="{{ route('monitoring.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <form method="GET" action="{{ route('monitoring.products') }}" class="flex gap-2 rounded-md border border-jamu-border bg-jamu-surface p-4">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kode atau nama produk" class="flex-1 rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
            <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white">Cari</button>
        </form>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Produk</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Satuan</th>
                    <th class="px-4 py-3 text-right">Stok</th>
                    <th class="px-4 py-3 text-right">Minimum</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse ($products as $product)
                    @php
                        $status = $product->stok_terkini === 0 ? 'Stok Habis' : ($product->stok_terkini <= $product->stok_minimum ? 'Stok Menipis' : 'Aman');
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $product->kode_produk }}</td>
                        <td class="px-4 py-3">{{ $product->nama_produk }}</td>
                        <td class="px-4 py-3">{{ $product->kategori?->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $product->satuan?->nama_satuan ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ $product->stok_terkini }}</td>
                        <td class="px-4 py-3 text-right">{{ $product->stok_minimum }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-md border px-2 py-1 text-xs font-semibold {{ $status === 'Aman' ? 'border-green-200 bg-green-50 text-green-800' : ($status === 'Stok Habis' ? 'border-red-200 bg-red-50 text-red-800' : 'border-yellow-200 bg-yellow-50 text-yellow-800') }}">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-jamu-muted">Produk tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </section>
</x-layouts.app>

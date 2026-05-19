<x-layouts.app title="Detail Stok Masuk">
    <section class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">{{ $stokMasuk->nomor_transaksi }}</h2>
                <p class="text-sm text-jamu-muted">Detail transaksi stok masuk.</p>
            </div>
            <a href="{{ route('stok-masuk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <div class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-4">
            <div>
                <p class="text-xs text-jamu-muted">Tanggal</p>
                <p class="font-medium">{{ $stokMasuk->tanggal_masuk_formatted }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Supplier</p>
                <p class="font-medium">{{ $stokMasuk->supplier?->nama_supplier ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Dicatat Oleh</p>
                <p class="font-medium">{{ $stokMasuk->pengguna?->nama_lengkap ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Catatan</p>
                <p class="font-medium">{{ $stokMasuk->catatan ?: '-' }}</p>
            </div>
        </div>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Batch</th>
                    <th class="px-4 py-3">Expired</th>
                    <th class="px-4 py-3 text-right">Jumlah</th>
                    <th class="px-4 py-3 text-right">Harga Beli</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @foreach ($stokMasuk->detailStokMasuk as $detail)
                    <tr>
                        <td class="px-4 py-3">{{ $detail->produk?->nama_produk ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $detail->batch?->nomor_batch ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $detail->batch?->tanggal_expired?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format((float) $detail->harga_beli, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    </section>
</x-layouts.app>

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

        <div class="flex flex-col gap-3">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Batch Produk</h3>
                    <p class="text-sm text-jamu-muted">Daftar batch yang terhubung dengan produk ini.</p>
                </div>
                <a href="{{ route('batch.create') }}" class="w-fit rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Tambah Batch</a>
            </div>

            <x-table>
                <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                    <tr>
                        <th class="px-4 py-3">Nomor Batch</th>
                        <th class="px-4 py-3">Produksi</th>
                        <th class="px-4 py-3">Expired</th>
                        <th class="px-4 py-3">Stok Batch</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-jamu-border">
                    @forelse($produk->batches as $batch)
                        <tr class="hover:bg-jamu-bg">
                            <td class="px-4 py-3 font-medium">{{ $batch->nomor_batch }}</td>
                            <td class="px-4 py-3 text-jamu-muted">{{ $batch->tanggal_produksi?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-jamu-muted">{{ $batch->tanggal_expired?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ number_format((int) $batch->stok_batch, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @if($batch->tanggal_expired?->isPast())
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">Kedaluwarsa</span>
                                @elseif($batch->tanggal_expired?->lte(now()->addDays(30)))
                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">Segera Expired</span>
                                @else
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-jamu-muted">{{ $batch->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <a href="{{ route('batch.edit', $batch) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-jamu-muted">Belum ada batch yang terhubung dengan produk ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>
    </section>
</x-layouts.app>

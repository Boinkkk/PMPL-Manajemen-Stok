<x-layouts.app title="Data Batch">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Data Batch</h2>
                <p class="text-sm text-jamu-muted">Kelola batch produk dan tanggal kedaluwarsa dengan mudah.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('produk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali ke Produk</a>
                <a href="{{ route('batch.create') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Tambah Batch</a>
            </div>
        </div>

        <x-alert />

        <div class="grid gap-3 md:grid-cols-[1fr_auto]">
            <form action="{{ route('batch.index') }}" method="GET" class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <div class="flex flex-col gap-3 md:flex-row">
                    <input type="text" name="search" class="min-w-0 flex-1 rounded-md border-jamu-border bg-white px-3 py-2 text-sm" placeholder="Cari nomor batch, nama produk, atau kode produk..." value="{{ old('search', $search) }}">
                    <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Cari</button>
                    <a href="{{ route('batch.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Reset</a>
                </div>
            </form>

            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Batch</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($batches->total(), 0, ',', '.') }}</p>
            </div>
        </div>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Nomor Batch</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Produksi</th>
                    <th class="px-4 py-3">Expired</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse($batches as $index => $batch)
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3">{{ $batches->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium">{{ $batch->nomor_batch }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $batch->produk?->nama_produk ?? '-' }}</div>
                            <div class="text-xs text-jamu-muted">{{ $batch->produk?->kode_produk ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-jamu-muted">{{ $batch->tanggal_produksi?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-jamu-muted">{{ $batch->tanggal_expired?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
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
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('batch.edit', $batch) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Edit</a>
                                <form action="{{ route('batch.destroy', $batch) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus batch {{ $batch->nomor_batch }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-jamu-muted">Tidak ada batch yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $batches->links() }}
    </section>
</x-layouts.app>

<x-layouts.app title="Manajemen EOQ">
    @php
        $canManageEoq = auth()->user()?->canManageStock() === true;
    @endphp

    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Manajemen Data EOQ</h2>
                <p class="text-sm text-jamu-muted">Kelola data Economic Order Quantity berdasarkan permintaan tahunan, biaya pemesanan, dan biaya penyimpanan.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('monitoring.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
                @if ($canManageEoq)
                    <form method="POST" action="{{ route('monitoring.eoq.sync') }}" onsubmit="return confirm('Sinkronisasi akan memperbarui permintaan tahunan dan nilai EOQ dari transaksi stok keluar 365 hari terakhir. Lanjutkan?')">
                        @csrf
                        <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                            Sinkronisasi EOQ
                        </button>
                    </form>
                    <form action="{{ route('monitoring.eoq.update-stok-minimum') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                        Update Stok Minimum
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-4">
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Data EOQ</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($summary['total_data'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Rata-rata EOQ</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($summary['rata_rata_eoq'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-yellow-200 bg-yellow-50 p-4">
                <p class="text-sm text-yellow-800">Tanpa Permintaan</p>
                <p class="mt-1 text-2xl font-semibold text-yellow-900">{{ number_format($summary['tanpa_permintaan'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-orange-200 bg-orange-50 p-4">
                <p class="text-sm text-orange-800">Stok <= EOQ</p>
                <p class="mt-1 text-2xl font-semibold text-orange-900">{{ number_format($summary['perlu_reorder'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="rounded-md border border-jamu-border bg-jamu-surface p-4 text-sm text-jamu-muted">
            <p><span class="font-semibold text-jamu-text">Catatan:</span> sinkronisasi mengambil permintaan tahunan dari total stok keluar 365 hari terakhir. Biaya pemesanan dan penyimpanan yang sudah Anda ubah manual akan dipertahankan saat sinkronisasi berikutnya.</p>
        </div>

        <form method="GET" action="{{ route('monitoring.eoq.index') }}" class="grid gap-3 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-[1fr_auto_auto]">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kode atau nama produk..." class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
            <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Cari</button>
            <a href="{{ route('monitoring.eoq.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Reset</a>
        </form>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3 text-right">Stok</th>
                    <th class="px-4 py-3 text-right">Permintaan Tahunan</th>
                    <th class="px-4 py-3 text-right">Biaya Pemesanan</th>
                    <th class="px-4 py-3 text-right">Biaya Penyimpanan</th>
                    <th class="px-4 py-3 text-right">EOQ</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse ($dataEoq as $item)
                    @php($formId = 'eoq-update-'.$item->id_eoq)
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $item->produk?->nama_produk ?? '-' }}</div>
                            <div class="text-xs text-jamu-muted">{{ $item->produk?->kode_produk ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $item->produk?->kategori?->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($item->produk?->stok_terkini ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($canManageEoq)
                                <input form="{{ $formId }}" type="number" min="0" name="permintaan_tahunan" value="{{ old('permintaan_tahunan', $item->permintaan_tahunan) }}" class="w-32 rounded-md border-jamu-border bg-white px-3 py-2 text-right text-sm">
                            @else
                                {{ number_format($item->permintaan_tahunan ?? 0, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($canManageEoq)
                                <input form="{{ $formId }}" type="number" min="1" name="biaya_pemesanan" value="{{ old('biaya_pemesanan', $item->biaya_pemesanan) }}" class="w-32 rounded-md border-jamu-border bg-white px-3 py-2 text-right text-sm">
                            @else
                                Rp {{ number_format($item->biaya_pemesanan ?? 0, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($canManageEoq)
                                <input form="{{ $formId }}" type="number" min="1" name="biaya_penyimpanan" value="{{ old('biaya_penyimpanan', $item->biaya_penyimpanan) }}" class="w-32 rounded-md border-jamu-border bg-white px-3 py-2 text-right text-sm">
                            @else
                                Rp {{ number_format($item->biaya_penyimpanan ?? 0, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                {{ number_format($item->eoq ?? 0, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end">
                                @if ($canManageEoq)
                                    <form id="{{ $formId }}" method="POST" action="{{ route('monitoring.eoq.update', $item) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">
                                        <button type="submit" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Simpan</button>
                                    </form>
                                @else
                                    <span class="text-sm text-jamu-muted">Read-only</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-jamu-muted">
                            Belum ada data EOQ. Jalankan sinkronisasi untuk membuat data awal.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $dataEoq->links() }}
    </section>
</x-layouts.app>

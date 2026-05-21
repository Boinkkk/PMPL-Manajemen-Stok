@php
    $stokKeluar = $order->stokKeluar->first();
@endphp

<x-layouts.app title="Detail Order Distribusi">
    <section class="flex flex-col gap-5" x-data="{ approveOpen: false, rejectOpen: false, cancelOpen: false }">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">{{ $order->nomor_order }}</h2>
                <p class="text-sm text-jamu-muted">Detail order distribusi.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('order-distribusi.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
                <a href="{{ route('order-distribusi.print', $order) }}" target="_blank" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">
                    {{ $order->status === 'selesai' ? 'Cetak Surat Jalan' : 'Cetak' }}
                </a>

                @if ($order->status === 'pending' && auth()->user()?->canManageStock())
                    <a href="{{ route('order-distribusi.edit', $order) }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Edit</a>
                    <button type="button" @click="approveOpen = true" class="rounded-md bg-jamu-green px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Setujui</button>
                    <button type="button" @click="rejectOpen = true" class="rounded-md border border-red-200 px-4 py-2 text-sm text-red-700 hover:bg-red-50">Tolak</button>
                    <button type="button" @click="cancelOpen = true" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Batalkan</button>
                @endif

                @if ($order->status === 'ditolak' && auth()->user()?->canManageStock())
                    <form method="POST" action="{{ route('order-distribusi.reorder', $order) }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Buat Ulang</button>
                    </form>
                @endif

                @if ($order->status === 'selesai' && $stokKeluar)
                    <a href="{{ route('stok-keluar.show', $stokKeluar) }}" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Lihat Stok Keluar</a>
                @endif
            </div>
        </div>

        <div class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-4">
            <div>
                <p class="text-sm text-jamu-muted">Distributor</p>
                <p class="font-medium">{{ $order->distributor?->nama_distributor ?? '-' }}</p>
                <p class="text-sm text-jamu-muted">{{ $order->distributor?->alamat ?? '-' }}</p>
                <p class="text-sm text-jamu-muted">{{ $order->distributor?->kontak_person ?? $order->distributor?->telepon ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-jamu-muted">Tanggal Order</p>
                <p class="font-medium">{{ $order->tanggal_order_formatted }}</p>
                <p class="mt-3 text-sm text-jamu-muted">Tanggal Diproses</p>
                <p class="font-medium">{{ $order->tanggal_diproses_formatted ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-jamu-muted">Status</p>
                <div class="mt-1"><x-badge :status="$order->status" /></div>
                <p class="mt-3 text-sm text-jamu-muted">Dibuat Oleh</p>
                <p class="font-medium">{{ $order->pengguna?->nama_lengkap ?? '-' }}</p>
                <p class="text-sm text-jamu-muted">{{ $order->dibuat_pada_formatted }}</p>
            </div>
            <div>
                <p class="text-sm text-jamu-muted">Catatan</p>
                <p class="whitespace-pre-line font-medium">{{ $order->catatan ?: '-' }}</p>
            </div>
        </div>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3 text-right">Jumlah Diminta</th>
                    @if ($order->status !== 'pending')
                        <th class="px-4 py-3 text-right">Jumlah Disetujui</th>
                    @endif
                    <th class="px-4 py-3 text-right">Harga Satuan</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                    <th class="px-4 py-3">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @foreach ($order->detailOrders as $detail)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $detail->produk?->nama_produk ?? '-' }}</p>
                            <p class="text-xs text-jamu-muted">{{ $detail->produk?->kode_produk ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-right">{{ number_format($detail->jumlah_diminta, 0, ',', '.') }}</td>
                        @if ($order->status !== 'pending')
                            <td class="px-4 py-3 text-right">{{ number_format($detail->jumlah_disetujui, 0, ',', '.') }}</td>
                        @endif
                        <td class="px-4 py-3 text-right">Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $detail->catatan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-jamu-border bg-jamu-bg font-semibold">
                <tr>
                    <td colspan="{{ $order->status !== 'pending' ? 4 : 3 }}" class="px-4 py-3 text-right">Total</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($order->totalNilai(), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </x-table>

        @if ($order->status === 'selesai' && $stokKeluar)
            <div class="rounded-md border border-green-200 bg-green-50 p-4">
                <h3 class="font-semibold text-green-900">Informasi Stok Keluar</h3>
                <div class="mt-3 grid gap-3 text-sm md:grid-cols-3">
                    <p><span class="text-green-800">Nomor transaksi:</span> <a href="{{ route('stok-keluar.show', $stokKeluar) }}" class="font-semibold underline">{{ $stokKeluar->nomor_transaksi }}</a></p>
                    <p><span class="text-green-800">Tanggal:</span> {{ $stokKeluar->tanggal_keluar_formatted }}</p>
                    <p><span class="text-green-800">Detail:</span> {{ $stokKeluar->detailStokKeluar->count() }} baris batch</p>
                </div>
            </div>
        @endif

        @if ($order->status === 'pending' && auth()->user()?->canManageStock())
            <div x-cloak x-show="approveOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                <div @click.outside="approveOpen = false" class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-md bg-jamu-surface p-5 shadow-xl">
                    <h3 class="text-lg font-semibold">Konfirmasi Persetujuan</h3>
                    <p class="mt-1 text-sm text-jamu-muted">Jumlah disetujui tidak boleh melebihi jumlah diminta dan stok terkini.</p>

                    <form method="POST" action="{{ route('order-distribusi.approve', $order) }}" class="mt-4 flex flex-col gap-4">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-jamu-bg text-left text-xs uppercase text-jamu-muted">
                                    <tr>
                                        <th class="px-3 py-2">Produk</th>
                                        <th class="px-3 py-2 text-right">Diminta</th>
                                        <th class="px-3 py-2 text-right">Stok</th>
                                        <th class="px-3 py-2 text-right">Disetujui</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-jamu-border">
                                    @foreach ($order->detailOrders as $index => $detail)
                                        @php
                                            $stokTersedia = (int) ($stokByProduk[$detail->id_produk] ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2">
                                                {{ $detail->produk?->nama_produk ?? '-' }}
                                                @if ($stokTersedia < $detail->jumlah_diminta)
                                                    <p class="text-xs text-red-700">Stok tidak cukup. Setujui sebagian atau tolak semua.</p>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-right">{{ $detail->jumlah_diminta }}</td>
                                            <td class="px-3 py-2 text-right">{{ $stokTersedia }}</td>
                                            <td class="px-3 py-2 text-right">
                                                <input type="hidden" name="details[{{ $index }}][id_detail_order]" value="{{ $detail->id_detail_order }}">
                                                <input type="number" min="0" max="{{ min($stokTersedia, $detail->jumlah_diminta) }}" name="details[{{ $index }}][jumlah_disetujui]" value="{{ min($stokTersedia, $detail->jumlah_diminta) }}" class="w-28 rounded-md border-jamu-border px-3 py-2 text-right">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <label class="flex flex-col gap-1 text-sm">
                            Catatan Persetujuan
                            <textarea name="catatan_persetujuan" rows="3" class="rounded-md border-jamu-border px-3 py-2"></textarea>
                        </label>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="approveOpen = false" class="rounded-md border border-jamu-border px-4 py-2 text-sm">Batal</button>
                            <button type="submit" class="rounded-md bg-jamu-green px-4 py-2 text-sm font-semibold text-white">Konfirmasi Setujui</button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-cloak x-show="rejectOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                <div @click.outside="rejectOpen = false" class="w-full max-w-lg rounded-md bg-jamu-surface p-5 shadow-xl">
                    <h3 class="text-lg font-semibold">Tolak Order</h3>
                    <form method="POST" action="{{ route('order-distribusi.reject', $order) }}" class="mt-4 flex flex-col gap-4">
                        @csrf
                        <label class="flex flex-col gap-1 text-sm">
                            Alasan Penolakan
                            <textarea name="alasan" rows="4" required minlength="10" class="rounded-md border-jamu-border px-3 py-2"></textarea>
                        </label>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="rejectOpen = false" class="rounded-md border border-jamu-border px-4 py-2 text-sm">Batal</button>
                            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white">Tolak Semua</button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-cloak x-show="cancelOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                <div @click.outside="cancelOpen = false" class="w-full max-w-md rounded-md bg-jamu-surface p-5 shadow-xl">
                    <h3 class="text-lg font-semibold">Batalkan Order?</h3>
                    <p class="mt-2 text-sm text-jamu-muted">Order pending akan ditandai sebagai ditolak karena dibatalkan sebelum diproses.</p>
                    <form method="POST" action="{{ route('order-distribusi.cancel', $order) }}" class="mt-4 flex justify-end gap-2">
                        @csrf
                        <button type="button" @click="cancelOpen = false" class="rounded-md border border-jamu-border px-4 py-2 text-sm">Tidak</button>
                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white">Batalkan Order</button>
                    </form>
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>

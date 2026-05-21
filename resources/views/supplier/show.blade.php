<x-layouts.app :title="'Detail Supplier '.$supplier->kode_supplier">
    <section class="flex flex-col gap-5" x-data="{ deleteModal: false, confirmation: '' }">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">{{ $supplier->nama_supplier }}</h2>
                <p class="text-sm text-jamu-muted">{{ $supplier->kode_supplier }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('supplier.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
                <a href="{{ route('supplier.edit', $supplier) }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Edit</a>
                @if (auth()->user()?->isAdministrator())
                    @if ($hasTransactions)
                        <button type="button" disabled title="Supplier tidak dapat dihapus karena memiliki riwayat transaksi. Hubungi Administrator jika diperlukan." class="cursor-not-allowed rounded-md border border-red-100 px-4 py-2 text-sm text-red-300">Hapus</button>
                    @else
                        <button type="button" @click="deleteModal = true" class="rounded-md border border-red-200 px-4 py-2 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                    @endif
                @endif
            </div>
        </div>

        @if ($hasTransactions)
            <div class="rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-900">
                Supplier ini memiliki riwayat transaksi stok masuk sehingga tidak dapat dihapus.
            </div>
        @endif

        <div class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-4">
            <div>
                <p class="text-xs text-jamu-muted">Kode Supplier</p>
                <p class="font-medium">{{ $supplier->kode_supplier }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Telepon</p>
                <p class="font-medium">{{ $supplier->telepon ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Email</p>
                <p class="font-medium">{{ $supplier->email ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Kontak Person</p>
                <p class="font-medium">{{ $supplier->kontak_person ?: '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-jamu-muted">Alamat</p>
                <p class="font-medium">{{ $supplier->alamat ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Tanggal Ditambahkan</p>
                <p class="font-medium">{{ $supplier->dibuat_pada_formatted }}</p>
            </div>
            <div>
                <p class="text-xs text-jamu-muted">Ditambahkan Oleh</p>
                <p class="font-medium">{{ $creatorName ?: '-' }}</p>
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-5">
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Transaksi</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($stats['total_transaksi'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Unit</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($stats['total_unit'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Nilai</p>
                <p class="mt-1 text-2xl font-semibold">{{ $service->rupiah($stats['total_nilai']) }}</p>
            </div>
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Transaksi Terakhir</p>
                <p class="mt-1 font-semibold">{{ $stats['transaksi_terakhir']?->nomor_transaksi ?? '-' }}</p>
                <p class="text-xs text-jamu-muted">{{ $service->tanggal($stats['transaksi_terakhir']?->tanggal_masuk ?? null) }}</p>
            </div>
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Produk Tersering</p>
                <p class="mt-1 font-semibold">{{ $stats['produk_tersering'] ?: '-' }}</p>
            </div>
        </div>

        <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="font-semibold">Riwayat Transaksi Stok Masuk</h3>
                    <p class="text-sm text-jamu-muted">FR-19: riwayat transaksi supplier.</p>
                </div>
                <form method="GET" action="{{ route('supplier.show', $supplier) }}" class="grid gap-2 md:grid-cols-4">
                    <input type="text" name="q_transaksi" value="{{ $filters['q_transaksi'] ?? '' }}" placeholder="Nomor transaksi" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white">Filter</button>
                </form>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-jamu-border text-sm">
                    <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                        <tr>
                            <th class="px-4 py-3">Nomor</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-right">Jumlah Item</th>
                            <th class="px-4 py-3 text-right">Total Unit</th>
                            <th class="px-4 py-3 text-right">Total Nilai</th>
                            <th class="px-4 py-3">Dicatat Oleh</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-jamu-border">
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $transaction->nomor_transaksi }}</td>
                                <td class="px-4 py-3">{{ $service->tanggal($transaction->tanggal_masuk) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((int) $transaction->jumlah_item, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((int) $transaction->total_unit, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">{{ $service->rupiah($transaction->total_nilai) }}</td>
                                <td class="px-4 py-3">{{ $transaction->dicatat_oleh ?: '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('stok-masuk.show', $transaction->id_stok_masuk) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Lihat Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-jamu-muted">Belum ada transaksi stok masuk untuk supplier ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>

        <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
            <h3 class="font-semibold">Produk yang Pernah Dipasok</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-jamu-border text-sm">
                    <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3 text-right">Total Unit</th>
                            <th class="px-4 py-3">Terakhir Dipasok</th>
                            <th class="px-4 py-3 text-right">Stok Terkini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-jamu-border">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $product->nama_produk }}</td>
                                <td class="px-4 py-3">{{ $product->nama_kategori ?: '-' }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((int) $product->total_unit, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $service->tanggal($product->terakhir_dipasok) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((int) $product->stok_terkini, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-jamu-muted">Belum ada produk yang pernah dipasok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-cloak x-show="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="deleteModal = false" class="w-full max-w-md rounded-md border border-jamu-border bg-jamu-surface p-5 shadow-xl">
                <h3 class="text-lg font-semibold">Hapus Supplier</h3>
                <p class="mt-2 text-sm text-jamu-muted">Ketik nama supplier untuk menghapus permanen data ini.</p>
                <form method="POST" action="{{ route('supplier.destroy', $supplier) }}" class="mt-4 flex flex-col gap-3">
                    @csrf
                    @method('DELETE')
                    <div class="rounded-md bg-jamu-bg px-3 py-2 text-sm font-semibold">{{ $supplier->nama_supplier }}</div>
                    <input type="text" x-model="confirmation" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm" placeholder="Nama supplier">
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="deleteModal = false" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-bg">Batal</button>
                        <button type="submit" :disabled="confirmation !== @js($supplier->nama_supplier)" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:bg-red-200">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>

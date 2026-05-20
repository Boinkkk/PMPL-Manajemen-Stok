@php
    $activeFilters = collect($filters)->filter(fn ($value, $key) => filled($value) && ! in_array($key, ['page'], true));
@endphp

<x-layouts.app :title="$definition['title']">
    <section class="flex flex-col gap-5" x-data="{ filterOpen: true }">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between print:hidden">
            <div>
                <h2 class="text-2xl font-semibold">{{ $definition['title'] }}</h2>
                <p class="text-sm text-jamu-muted">{{ $definition['description'] }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if (auth()->user()?->canExportOrders())
                    <a href="{{ route('laporan.excel', array_merge(['type' => $type], request()->query())) }}" class="rounded-md bg-jamu-green px-4 py-2 text-sm font-semibold text-white">Excel</a>
                    <a href="{{ route('laporan.pdf', array_merge(['type' => $type], request()->query())) }}" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white">PDF</a>
                    <a href="{{ route('laporan.print', array_merge(['type' => $type], request()->query())) }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Cetak</a>
                @endif
                <a href="{{ route('laporan.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Daftar Laporan</a>
            </div>
        </div>

        <div class="hidden print:block">
            <h1 class="text-2xl font-bold">{{ $definition['title'] }}</h1>
            <p>Dicetak: {{ now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i') }}</p>
        </div>

        <div class="rounded-md border border-jamu-border bg-jamu-surface print:hidden">
            <button type="button" @click="filterOpen = !filterOpen" class="flex w-full items-center justify-between px-4 py-3 font-semibold">
                Filter Laporan
                <span x-text="filterOpen ? 'Tutup' : 'Buka'"></span>
            </button>
            <form x-show="filterOpen" method="GET" action="{{ route('laporan.show', $type) }}" class="grid gap-3 border-t border-jamu-border p-4 md:grid-cols-4">
                <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? now('Asia/Jakarta')->startOfMonth()->toDateString() }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? now('Asia/Jakarta')->toDateString() }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">

                <select name="id_kategori" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id_kategori }}" @selected(($filters['id_kategori'] ?? '') == $category->id_kategori)>{{ $category->nama_kategori }}</option>
                    @endforeach
                </select>

                <select name="id_produk" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="">Semua produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id_produk }}" @selected(($filters['id_produk'] ?? '') == $product->id_produk)>{{ $product->kode_produk }} - {{ $product->nama_produk }}</option>
                    @endforeach
                </select>

                <select name="id_distributor" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="">Semua distributor</option>
                    @foreach ($distributors as $distributor)
                        <option value="{{ $distributor->id_distributor }}" @selected(($filters['id_distributor'] ?? '') == $distributor->id_distributor)>{{ $distributor->nama_distributor }}</option>
                    @endforeach
                </select>

                <select name="id_supplier" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="">Semua supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id_supplier }}" @selected(($filters['id_supplier'] ?? '') == $supplier->id_supplier)>{{ $supplier->nama_supplier }}</option>
                    @endforeach
                </select>

                <select name="status" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="">Semua status order</option>
                    @foreach (['pending', 'disetujui', 'ditolak', 'selesai'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>

                <select name="status_stok" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="">Semua status stok</option>
                    <option value="habis" @selected(($filters['status_stok'] ?? '') === 'habis')>Habis</option>
                    <option value="menipis" @selected(($filters['status_stok'] ?? '') === 'menipis')>Menipis</option>
                    <option value="normal" @selected(($filters['status_stok'] ?? '') === 'normal')>Normal</option>
                </select>

                <select name="jenis_mutasi" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="semua" @selected(($filters['jenis_mutasi'] ?? '') === 'semua')>Semua mutasi</option>
                    <option value="masuk" @selected(($filters['jenis_mutasi'] ?? '') === 'masuk')>Masuk saja</option>
                    <option value="keluar" @selected(($filters['jenis_mutasi'] ?? '') === 'keluar')>Keluar saja</option>
                </select>

                <select name="kondisi" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <option value="30_hari" @selected(($filters['kondisi'] ?? '') === '30_hari')>Akan expired 30 hari</option>
                    <option value="expired" @selected(($filters['kondisi'] ?? '') === 'expired')>Sudah expired</option>
                    <option value="7_hari" @selected(($filters['kondisi'] ?? '') === '7_hari')>Akan expired 7 hari</option>
                    <option value="60_hari" @selected(($filters['kondisi'] ?? '') === '60_hari')>Akan expired 60 hari</option>
                    <option value="custom" @selected(($filters['kondisi'] ?? '') === 'custom')>Custom tanggal</option>
                </select>

                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama/kode" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">

                <select name="per_page" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    @foreach ([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected(($filters['per_page'] ?? 25) == $size)>{{ $size }} per halaman</option>
                    @endforeach
                </select>

                <div class="flex gap-2 md:col-span-4">
                    <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white">Terapkan Filter</button>
                    <a href="{{ route('laporan.show', $type) }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-bg">Reset Filter</a>
                </div>
            </form>
        </div>

        @if ($activeFilters->isNotEmpty())
            <div class="flex flex-wrap gap-2 print:hidden">
                @foreach ($activeFilters as $key => $value)
                    <span class="rounded-md border border-jamu-border bg-jamu-surface px-2 py-1 text-xs">{{ str_replace('_', ' ', $key) }}: {{ $value }}</span>
                @endforeach
            </div>
        @endif

        <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
            <div class="mb-3 flex items-center justify-between gap-3 text-sm text-jamu-muted">
                <span>Total data: {{ $rows->total() }}</span>
                <span>Halaman {{ $rows->currentPage() }} dari {{ $rows->lastPage() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                        <tr>
                            @foreach ($columns as $column)
                                <th class="px-4 py-3">
                                    <a href="{{ route('laporan.show', array_merge(['type' => $type], request()->query(), ['sort' => $column, 'direction' => (request('direction') === 'asc' ? 'desc' : 'asc')])) }}">
                                        {{ str($column)->replace('_', ' ')->title() }}
                                    </a>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-jamu-border">
                        @forelse ($rows as $row)
                            <tr class="hover:bg-jamu-bg">
                                @foreach ($columns as $column)
                                    @php $value = $row->{$column} ?? null; @endphp
                                    <td class="px-4 py-3">
                                        @if (str_contains($column, 'nilai') || str_contains($column, 'harga') || str_contains($column, 'rata_rata'))
                                            Rp {{ number_format((float) $value, 0, ',', '.') }}
                                        @elseif (str_contains($column, 'tanggal') && filled($value))
                                            {{ \Illuminate\Support\Carbon::parse($value)->locale('id')->translatedFormat('d F Y') }}
                                        @elseif ($column === 'status')
                                            <x-badge :status="$value" />
                                        @else
                                            {{ $value ?? '-' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) }}" class="px-4 py-12 text-center text-jamu-muted">
                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-jamu-bg text-xl">i</div>
                                    <p class="font-semibold">Tidak ada data untuk periode dan filter yang dipilih</p>
                                    <p class="text-sm">Coba ubah rentang tanggal atau hapus filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (! empty($summary))
                        <tfoot class="border-t border-jamu-border bg-jamu-bg font-semibold">
                            @foreach ($summary as $label => $value)
                                <tr>
                                    <td colspan="{{ max(1, count($columns) - 1) }}" class="px-4 py-2 text-right">{{ $label }}</td>
                                    <td class="px-4 py-2">
                                        @if (str_contains(strtolower($label), 'nilai'))
                                            Rp {{ number_format((float) $value, 0, ',', '.') }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div class="print:hidden">{{ $rows->links() }}</div>
    </section>
</x-layouts.app>

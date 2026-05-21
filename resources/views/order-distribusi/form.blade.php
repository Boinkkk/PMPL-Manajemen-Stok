@php
    $productOptions = $products->map(fn ($produk) => [
        'id_produk' => $produk->id_produk,
        'label' => "{$produk->kode_produk} - {$produk->nama_produk}",
        'nama_produk' => $produk->nama_produk,
        'harga_satuan' => (float) $produk->harga_satuan,
        'stok_terkini' => (int) $produk->stok_terkini,
        'stok_minimum' => (int) $produk->stok_minimum,
        'satuan' => $produk->satuan?->nama_satuan ?? '',
    ])->values();

    $initialDetails = old('details', $order?->detailOrders?->map(fn ($detail) => [
        'id_produk' => $detail->id_produk,
        'jumlah_diminta' => $detail->jumlah_diminta,
        'harga_satuan' => (float) $detail->harga_satuan,
        'catatan' => $detail->catatan,
    ])->values()->all() ?? [[
        'id_produk' => '',
        'jumlah_diminta' => 1,
        'harga_satuan' => 0,
        'catatan' => '',
    ]]);
@endphp

<x-layouts.app :title="$order ? 'Edit Order Distribusi' : 'Buat Order Distribusi'">
    <section
        class="flex flex-col gap-5"
        x-data="orderForm({
            products: @js($productOptions),
            rows: @js($initialDetails),
        })"
        x-init="init()"
    >
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">{{ $order ? 'Edit Order Distribusi' : 'Buat Order Distribusi' }}</h2>
                <p class="text-sm text-jamu-muted">Stok dicek sebagai peringatan saat input dan divalidasi ulang saat persetujuan.</p>
            </div>
            <a href="{{ $order ? route('order-distribusi.show', $order) : route('order-distribusi.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Batal</a>
        </div>

        @if ($errors->any())
            <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-semibold">Periksa kembali data order.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $action }}" class="flex flex-col gap-5">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-2">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Nomor Order</label>
                    <input type="text" value="{{ $nomorOrder }}" readonly class="rounded-md border-jamu-border bg-jamu-bg px-3 py-2 text-sm">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Tanggal Order</label>
                    <input type="date" name="tanggal_order" value="{{ old('tanggal_order', $order?->tanggal_order?->toDateString() ?? today()->toDateString()) }}" max="{{ today()->toDateString() }}" required class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <x-form.error name="tanggal_order" />
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Distributor</label>
                    <select name="id_distributor" required class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                        <option value="">Pilih distributor</option>
                        @foreach ($distributors as $distributor)
                            <option value="{{ $distributor->id_distributor }}" @selected(old('id_distributor', $order?->id_distributor) == $distributor->id_distributor)>
                                {{ $distributor->kode_distributor }} - {{ $distributor->nama_distributor }}
                            </option>
                        @endforeach
                    </select>
                    <x-form.error name="id_distributor" />
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">Catatan</label>
                    <textarea name="catatan" rows="3" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">{{ old('catatan', $order?->catatan) }}</textarea>
                    <x-form.error name="catatan" />
                </div>
            </div>

            <div class="rounded-md border border-jamu-border bg-jamu-surface">
                <div class="flex items-center justify-between gap-3 border-b border-jamu-border p-4">
                    <div>
                        <h3 class="font-semibold">Detail Produk</h3>
                        <p class="text-sm text-jamu-muted">Produk yang sama hanya boleh dipilih satu kali.</p>
                    </div>
                    <button type="button" @click="addRow()" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Tambah Baris</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">Jumlah</th>
                                <th class="px-4 py-3">Harga Satuan</th>
                                <th class="px-4 py-3">Subtotal</th>
                                <th class="px-4 py-3">Catatan</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-jamu-border">
                            <template x-for="(row, index) in rows" :key="row.key">
                                <tr>
                                    <td class="min-w-72 px-4 py-3 align-top">
                                        <select x-model="row.id_produk" @change="syncProduct(row)" :name="`details[${index}][id_produk]`" required class="w-full rounded-md border-jamu-border bg-white px-3 py-2">
                                            <option value="">Pilih produk</option>
                                            <template x-for="product in products" :key="product.id_produk">
                                                <option :value="product.id_produk" x-text="product.label"></option>
                                            </template>
                                        </select>
                                        <div class="mt-1 text-xs">
                                            <p class="text-jamu-muted" x-show="selectedProduct(row)" x-text="stockText(row)"></p>
                                            <p class="text-red-700" x-show="selectedProduct(row) && selectedProduct(row).stok_terkini === 0">Stok habis</p>
                                            <p class="text-yellow-800" x-show="isLowStock(row)">Stok menipis <span x-text="`(sisa: ${selectedProduct(row).stok_terkini})`"></span></p>
                                            <p class="text-red-700" x-show="isInsufficient(row)">Stok tidak mencukupi, order tetap bisa disimpan.</p>
                                            <p class="text-red-700" x-show="isDuplicate(row)">Produk duplikat dalam order ini.</p>
                                        </div>
                                    </td>
                                    <td class="min-w-28 px-4 py-3 align-top">
                                        <input type="number" min="1" x-model.number="row.jumlah_diminta" :name="`details[${index}][jumlah_diminta]`" required class="w-full rounded-md border-jamu-border px-3 py-2">
                                    </td>
                                    <td class="min-w-36 px-4 py-3 align-top">
                                        <input type="number" min="1" step="0.01" x-model.number="row.harga_satuan" :name="`details[${index}][harga_satuan]`" required class="w-full rounded-md border-jamu-border px-3 py-2">
                                    </td>
                                    <td class="min-w-36 px-4 py-3 align-top font-semibold">
                                        <span x-text="formatCurrency(subtotal(row))"></span>
                                    </td>
                                    <td class="min-w-48 px-4 py-3 align-top">
                                        <input type="text" x-model="row.catatan" :name="`details[${index}][catatan]`" class="w-full rounded-md border-jamu-border px-3 py-2">
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <button type="button" @click="removeRow(index)" :disabled="rows.length === 1" class="rounded-md border border-red-200 px-3 py-2 text-red-700 disabled:cursor-not-allowed disabled:opacity-50">Hapus</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-2 border-t border-jamu-border p-4 md:flex-row md:items-center md:justify-between">
                    <p class="text-sm text-red-700" x-show="hasDuplicate()">Ada produk duplikat. Hapus salah satu baris sebelum menyimpan.</p>
                    <p class="text-lg font-semibold">Total Order: <span x-text="formatCurrency(total())"></span></p>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('order-distribusi.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Batal</a>
                <button type="submit" :disabled="hasDuplicate()" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light disabled:cursor-not-allowed disabled:opacity-50">
                    Simpan Order
                </button>
            </div>
        </form>
    </section>

    <script>
        function orderForm(config) {
            return {
                products: config.products,
                rows: [],
                init() {
                    this.rows = config.rows.map((row) => ({
                        key: crypto.randomUUID(),
                        id_produk: row.id_produk ? String(row.id_produk) : '',
                        jumlah_diminta: Number(row.jumlah_diminta || 1),
                        harga_satuan: Number(row.harga_satuan || 0),
                        catatan: row.catatan || '',
                    }));
                },
                addRow() {
                    this.rows.push({ key: crypto.randomUUID(), id_produk: '', jumlah_diminta: 1, harga_satuan: 0, catatan: '' });
                },
                removeRow(index) {
                    if (this.rows.length > 1) {
                        this.rows.splice(index, 1);
                    }
                },
                selectedProduct(row) {
                    return this.products.find((product) => String(product.id_produk) === String(row.id_produk));
                },
                syncProduct(row) {
                    const product = this.selectedProduct(row);
                    if (product && Number(row.harga_satuan) <= 0) {
                        row.harga_satuan = product.harga_satuan;
                    }
                },
                stockText(row) {
                    const product = this.selectedProduct(row);
                    return product ? `Stok: ${product.stok_terkini} ${product.satuan}` : '';
                },
                isLowStock(row) {
                    const product = this.selectedProduct(row);
                    return product && product.stok_terkini > 0 && product.stok_terkini <= product.stok_minimum;
                },
                isInsufficient(row) {
                    const product = this.selectedProduct(row);
                    return product && Number(row.jumlah_diminta || 0) > product.stok_terkini;
                },
                isDuplicate(row) {
                    if (! row.id_produk) {
                        return false;
                    }

                    return this.rows.filter((item) => String(item.id_produk) === String(row.id_produk)).length > 1;
                },
                hasDuplicate() {
                    return this.rows.some((row) => this.isDuplicate(row));
                },
                subtotal(row) {
                    return Number(row.jumlah_diminta || 0) * Number(row.harga_satuan || 0);
                },
                total() {
                    return this.rows.reduce((sum, row) => sum + this.subtotal(row), 0);
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
                },
            };
        }
    </script>
</x-layouts.app>

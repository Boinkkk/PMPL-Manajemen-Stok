<x-layouts.app title="Tambah Stok Keluar">
    <section
        x-data="stokKeluarForm(@js($products->map(fn ($product) => [
            'id_produk' => $product->id_produk,
            'nama_produk' => $product->nama_produk,
            'harga_satuan' => (float) $product->harga_satuan,
            'stok_terkini' => (int) $product->stok_terkini,
            'satuan' => $product->satuan?->singkatan,
        ])->values()))"
        class="flex flex-col gap-4"
    >
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Tambah Stok Keluar</h2>
                <p class="text-sm text-jamu-muted">Catat distribusi stok ke distributor.</p>
            </div>
            <a href="{{ route('stok-keluar.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <form method="POST" action="{{ route('stok-keluar.store') }}" class="flex flex-col gap-4">
            @csrf

            <div class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-4">
                <label class="flex flex-col gap-1 text-sm">
                    <span class="font-medium">Distributor</span>
                    <select name="id_distributor" required class="rounded-md border-jamu-border bg-white px-3 py-2">
                        <option value="">Pilih distributor</option>
                        @foreach ($distributors as $distributor)
                            <option value="{{ $distributor->id_distributor }}" @selected(old('id_distributor') == $distributor->id_distributor)>
                                {{ $distributor->nama_distributor }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="flex flex-col gap-1 text-sm">
                    <span class="font-medium">Order Distribusi</span>
                    <select name="id_order" class="rounded-md border-jamu-border bg-white px-3 py-2">
                        <option value="">Tanpa order</option>
                        @foreach ($orders as $order)
                            <option value="{{ $order->id_order }}" @selected(old('id_order') == $order->id_order)>
                                {{ $order->nomor_order }} - {{ $order->status }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="flex flex-col gap-1 text-sm">
                    <span class="font-medium">Tanggal Keluar</span>
                    <input type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', now()->toDateString()) }}" required class="rounded-md border-jamu-border bg-white px-3 py-2">
                </label>

                <label class="flex flex-col gap-1 text-sm">
                    <span class="font-medium">Catatan</span>
                    <input type="text" name="catatan" value="{{ old('catatan') }}" class="rounded-md border-jamu-border bg-white px-3 py-2" placeholder="Opsional">
                </label>
            </div>

            <div class="rounded-md border border-jamu-border bg-jamu-surface">
                <div class="flex items-center justify-between gap-3 border-b border-jamu-border p-4">
                    <h3 class="font-semibold">Detail Produk</h3>
                    <button type="button" x-on:click="addRow()" class="rounded-md bg-jamu-green px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">
                        Tambah Baris
                    </button>
                </div>

                <div class="flex flex-col gap-4 p-4">
                    <template x-for="(row, index) in rows" :key="row.key">
                        <div class="grid gap-3 rounded-md border border-jamu-border bg-white p-3 lg:grid-cols-12">
                            <label class="flex flex-col gap-1 text-sm lg:col-span-3">
                                <span class="font-medium">Produk</span>
                                <select x-model="row.id_produk" x-on:change="productChanged(row)" :name="`details[${index}][id_produk]`" required class="rounded-md border-jamu-border px-3 py-2">
                                    <option value="">Pilih produk</option>
                                    <template x-for="product in products" :key="product.id_produk">
                                        <option :value="product.id_produk" x-text="`${product.nama_produk} - stok ${product.stok_terkini}`"></option>
                                    </template>
                                </select>
                            </label>

                            <label class="flex flex-col gap-1 text-sm lg:col-span-2">
                                <span class="font-medium">Batch</span>
                                <select x-model="row.id_batch" :name="`details[${index}][id_batch]`" required class="rounded-md border-jamu-border px-3 py-2" :disabled="row.loadingBatch || !row.id_produk">
                                    <option value="" x-text="row.loadingBatch ? 'Memuat batch...' : 'Pilih batch'"></option>
                                    <template x-for="batch in row.batches" :key="batch.id_batch">
                                        <option :value="batch.id_batch" x-text="`${batch.nomor_batch} - ${batch.tanggal_expired_formatted}`"></option>
                                    </template>
                                </select>
                            </label>

                            <label class="flex flex-col gap-1 text-sm">
                                <span class="font-medium">Stok</span>
                                <input type="text" :value="row.stok" readonly class="rounded-md border-jamu-border bg-jamu-bg px-3 py-2 text-right">
                            </label>

                            <label class="flex flex-col gap-1 text-sm">
                                <span class="font-medium">Jumlah</span>
                                <input type="number" min="1" x-model.number="row.jumlah" :name="`details[${index}][jumlah]`" required class="rounded-md border-jamu-border px-3 py-2 text-right">
                            </label>

                            <label class="flex flex-col gap-1 text-sm lg:col-span-2">
                                <span class="font-medium">Harga Jual</span>
                                <input type="number" min="0" step="0.01" x-model.number="row.harga_jual" :name="`details[${index}][harga_jual]`" required class="rounded-md border-jamu-border px-3 py-2 text-right">
                            </label>

                            <div class="flex items-end justify-between gap-2 lg:col-span-3">
                                <div class="text-sm">
                                    <p class="text-jamu-muted">Subtotal</p>
                                    <p class="font-semibold" x-text="formatCurrency(row.jumlah * row.harga_jual)"></p>
                                    <p x-show="Number(row.jumlah) > Number(row.stok)" class="text-xs text-red-700">Jumlah melebihi stok tersedia.</p>
                                </div>
                                <button type="button" x-on:click="removeRow(index)" class="rounded-md border border-red-200 px-3 py-2 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-end border-t border-jamu-border pt-4 text-right">
                        <div>
                            <p class="text-sm text-jamu-muted">Total Estimasi</p>
                            <p class="text-xl font-semibold" x-text="formatCurrency(total())"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('stok-keluar.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Batal</a>
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                    Simpan Stok Keluar
                </button>
            </div>
        </form>
    </section>

    <script>
        function stokKeluarForm(products) {
            const blankRow = () => ({
                key: crypto.randomUUID(),
                id_produk: '',
                id_batch: '',
                batches: [],
                stok: 0,
                jumlah: 1,
                harga_jual: 0,
                loadingBatch: false,
            });

            return {
                products,
                rows: [blankRow()],
                addRow() {
                    this.rows.push(blankRow());
                },
                removeRow(index) {
                    if (this.rows.length > 1) {
                        this.rows.splice(index, 1);
                    }
                },
                async productChanged(row) {
                    const product = this.products.find((item) => Number(item.id_produk) === Number(row.id_produk));
                    row.harga_jual = product ? Number(product.harga_satuan) : 0;
                    row.id_batch = '';
                    row.batches = [];
                    row.stok = 0;

                    if (!row.id_produk) {
                        return;
                    }

                    row.loadingBatch = true;
                    const [batchResponse, stokResponse] = await Promise.all([
                        fetch(`/api/produk/${row.id_produk}/batch`),
                        fetch(`/api/produk/${row.id_produk}/stok`),
                    ]);

                    const batchJson = await batchResponse.json();
                    const stokJson = await stokResponse.json();

                    row.batches = batchJson.data ?? [];
                    row.stok = stokJson.data?.stok_terkini ?? 0;
                    row.loadingBatch = false;
                },
                total() {
                    return this.rows.reduce((sum, row) => sum + (Number(row.jumlah || 0) * Number(row.harga_jual || 0)), 0);
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
                },
            };
        }
    </script>
</x-layouts.app>

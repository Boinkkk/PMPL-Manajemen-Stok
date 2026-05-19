<x-layouts.app title="Tambah Stok Masuk">
    <section
        x-data="stokMasukForm(@js($products->map(fn ($product) => [
            'id_produk' => $product->id_produk,
            'nama_produk' => $product->nama_produk,
            'harga_satuan' => (float) $product->harga_satuan,
            'satuan' => $product->satuan?->singkatan,
        ])->values()))"
        class="flex flex-col gap-4"
    >
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Tambah Stok Masuk</h2>
                <p class="text-sm text-jamu-muted">Catat penerimaan stok dari supplier.</p>
            </div>
            <a href="{{ route('stok-masuk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <form method="POST" action="{{ route('stok-masuk.store') }}" class="flex flex-col gap-4">
            @csrf

            <div class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-3">
                <label class="flex flex-col gap-1 text-sm">
                    <span class="font-medium">Supplier</span>
                    <select name="id_supplier" required class="rounded-md border-jamu-border bg-white px-3 py-2">
                        <option value="">Pilih supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id_supplier }}" @selected(old('id_supplier') == $supplier->id_supplier)>
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="flex flex-col gap-1 text-sm">
                    <span class="font-medium">Tanggal Masuk</span>
                    <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', now()->toDateString()) }}" required class="rounded-md border-jamu-border bg-white px-3 py-2">
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
                                <select x-model="row.id_produk" x-on:change="setHarga(row)" :name="`details[${index}][id_produk]`" required class="rounded-md border-jamu-border px-3 py-2">
                                    <option value="">Pilih produk</option>
                                    <template x-for="product in products" :key="product.id_produk">
                                        <option :value="product.id_produk" x-text="`${product.nama_produk} (${product.satuan ?? '-'})`"></option>
                                    </template>
                                </select>
                            </label>

                            <label class="flex flex-col gap-1 text-sm lg:col-span-2">
                                <span class="font-medium">Nomor Batch</span>
                                <input type="text" x-model="row.nomor_batch" :name="`details[${index}][nomor_batch]`" required class="rounded-md border-jamu-border px-3 py-2">
                            </label>

                            <label class="flex flex-col gap-1 text-sm lg:col-span-2">
                                <span class="font-medium">Produksi</span>
                                <input type="date" x-model="row.tanggal_produksi" :name="`details[${index}][tanggal_produksi]`" class="rounded-md border-jamu-border px-3 py-2">
                            </label>

                            <label class="flex flex-col gap-1 text-sm lg:col-span-2">
                                <span class="font-medium">Expired</span>
                                <input type="date" x-model="row.tanggal_expired" :name="`details[${index}][tanggal_expired]`" required class="rounded-md border-jamu-border px-3 py-2">
                            </label>

                            <label class="flex flex-col gap-1 text-sm">
                                <span class="font-medium">Jumlah</span>
                                <input type="number" min="1" x-model.number="row.jumlah" :name="`details[${index}][jumlah]`" required class="rounded-md border-jamu-border px-3 py-2 text-right">
                            </label>

                            <label class="flex flex-col gap-1 text-sm">
                                <span class="font-medium">Harga</span>
                                <input type="number" min="0" step="0.01" x-model.number="row.harga_beli" :name="`details[${index}][harga_beli]`" required class="rounded-md border-jamu-border px-3 py-2 text-right">
                            </label>

                            <div class="flex items-end justify-between gap-2">
                                <div class="text-right text-sm">
                                    <p class="text-jamu-muted">Subtotal</p>
                                    <p class="font-semibold" x-text="formatCurrency(row.jumlah * row.harga_beli)"></p>
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
                <a href="{{ route('stok-masuk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Batal</a>
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                    Simpan Stok Masuk
                </button>
            </div>
        </form>
    </section>

    <script>
        function stokMasukForm(products) {
            const blankRow = () => ({
                key: crypto.randomUUID(),
                id_produk: '',
                nomor_batch: '',
                tanggal_produksi: '',
                tanggal_expired: '',
                jumlah: 1,
                harga_beli: 0,
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
                setHarga(row) {
                    const product = this.products.find((item) => Number(item.id_produk) === Number(row.id_produk));
                    row.harga_beli = product ? Number(product.harga_satuan) : 0;
                },
                total() {
                    return this.rows.reduce((sum, row) => sum + (Number(row.jumlah || 0) * Number(row.harga_beli || 0)), 0);
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
                },
            };
        }
    </script>
</x-layouts.app>

<x-layouts.app title="Edit Produk">
    <section class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Edit Produk</h2>
                <p class="text-sm text-jamu-muted">Perbarui informasi produk {{ $produk->nama_produk }}.</p>
            </div>
            <a href="{{ route('produk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <form action="{{ route('produk.update', $produk->id_produk) }}" method="POST" class="grid gap-4 rounded-md border border-jamu-border bg-jamu-surface p-5">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <x-form.input name="kode_produk" label="Kode Produk" :value="$produk->kode_produk" required />
                <x-form.input name="nama_produk" label="Nama Produk" :value="$produk->nama_produk" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <x-form.select name="id_kategori" label="Kategori" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id_kategori }}" @selected(old('id_kategori', $produk->id_kategori) == $kat->id_kategori)>{{ $kat->nama_kategori }}</option>
                    @endforeach
                </x-form.select>

                <x-form.select name="id_satuan" label="Satuan" required>
                    <option value="">Pilih Satuan</option>
                    @foreach($satuans as $satuan)
                        <option value="{{ $satuan->id_satuan }}" @selected(old('id_satuan', $produk->id_satuan) == $satuan->id_satuan)>{{ $satuan->nama_satuan }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <x-form.input name="harga_satuan" label="Harga Satuan" type="number" :value="$produk->harga_satuan" min="0" step="0.01" required />
                <x-form.input name="stok_terkini" label="Stok Terkini" type="number" :value="$produk->stok_terkini" min="0" required />
                <x-form.input name="stok_minimum" label="Stok Minimum" type="number" :value="$produk->stok_minimum" min="0" required />
            </div>

            <label class="flex flex-col gap-1 text-sm">
                <span class="font-medium text-jamu-text">Deskripsi</span>
                <textarea name="deskripsi" rows="4" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm shadow-sm focus:border-jamu-secondary focus:ring-jamu-secondary">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                <x-form.error name="deskripsi" />
            </label>

            <div class="flex justify-end gap-2">
                <a href="{{ route('produk.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-bg">Batal</a>
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Perbarui</button>
            </div>
        </form>
    </section>
</x-layouts.app>

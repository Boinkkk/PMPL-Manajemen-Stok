<x-layouts.app title="Tambah Batch">
    <section class="mx-auto flex max-w-4xl flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Tambah Batch</h2>
                <p class="text-sm text-jamu-muted">Isi detail batch untuk produk yang tersedia.</p>
            </div>
            <a href="{{ route('batch.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <x-alert />

        <form action="{{ route('batch.store') }}" method="POST" class="rounded-md border border-jamu-border bg-jamu-surface p-5">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <x-form.select name="id_produk" label="Produk" required>
                    <option value="">Pilih Produk</option>
                    @foreach($produks as $produk)
                        <option value="{{ $produk->id_produk }}" @selected(old('id_produk') == $produk->id_produk)>
                            {{ $produk->kode_produk }} - {{ $produk->nama_produk }}
                        </option>
                    @endforeach
                </x-form.select>

                <x-form.input name="nomor_batch" label="Nomor Batch" placeholder="Contoh: BTH-001" required />

                <x-form.input name="tanggal_produksi" label="Tanggal Produksi" type="date" />
                <x-form.input name="tanggal_expired" label="Tanggal Expired" type="date" required />

                <label class="flex flex-col gap-1 text-sm md:col-span-2">
                    <span class="font-medium text-jamu-text">Keterangan</span>
                    <textarea name="keterangan" rows="4" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm shadow-sm focus:border-jamu-secondary focus:ring-jamu-secondary" placeholder="Keterangan batch (opsional)">{{ old('keterangan') }}</textarea>
                    <x-form.error name="keterangan" />
                </label>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Simpan Batch</button>
                <a href="{{ route('batch.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Batal</a>
            </div>
        </form>
    </section>
</x-layouts.app>

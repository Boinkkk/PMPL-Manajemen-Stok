<x-layouts.app title="Ajukan Retur Produk">
    <section class="mx-auto flex max-w-4xl flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Ajukan Retur Produk</h2>
                <p class="text-sm text-jamu-muted">Isi formulir retur untuk pengembalian produk cacat atau bermasalah.</p>
            </div>
            <a href="{{ route('retur.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <x-alert />

        <form action="{{ route('retur.store') }}" method="POST" enctype="multipart/form-data" class="rounded-md border border-jamu-border bg-jamu-surface p-5">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <x-form.select name="id_distributor" label="Distributor" required>
                    <option value="">Pilih distributor</option>
                    @foreach($distributors as $distributor)
                        <option value="{{ $distributor->id_distributor }}" @selected(old('id_distributor') == $distributor->id_distributor)>
                            {{ $distributor->nama_distributor }}
                        </option>
                    @endforeach
                </x-form.select>

                <x-form.select name="id_produk" label="Produk" required>
                    <option value="">Pilih produk</option>
                    @foreach($produks as $produk)
                        <option value="{{ $produk->id_produk }}" @selected(old('id_produk') == $produk->id_produk)>
                            {{ $produk->nama_produk }}
                        </option>
                    @endforeach
                </x-form.select>

                <x-form.input name="jumlah_retur" label="Jumlah Retur" type="number" min="1" required />

                <x-form.select name="id_supplier" label="Supplier Opsional">
                    <option value="">Gunakan supplier default</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id_supplier }}" @selected(old('id_supplier') == $supplier->id_supplier)>
                            {{ $supplier->nama_supplier }}
                        </option>
                    @endforeach
                </x-form.select>

                <label class="flex flex-col gap-1 text-sm md:col-span-2">
                    <span class="font-medium text-jamu-text">Alasan</span>
                    <textarea name="alasan" rows="4" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm shadow-sm focus:border-jamu-secondary focus:ring-jamu-secondary" placeholder="Jelaskan alasan retur produk" required>{{ old('alasan') }}</textarea>
                    <x-form.error name="alasan" />
                </label>

                <label class="flex flex-col gap-1 text-sm md:col-span-2">
                    <span class="font-medium text-jamu-text">Foto Bukti</span>
                    <input type="file" name="foto_bukti" accept="image/*" class="rounded-md border border-jamu-border bg-white px-3 py-2 text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-jamu-secondary file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-jamu-primary-dark">
                    <span class="text-xs text-jamu-muted">Opsional. Format gambar, maksimal 2 MB.</span>
                    <x-form.error name="foto_bukti" />
                </label>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Kirim Retur</button>
                <a href="{{ route('retur.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Batal</a>
            </div>
        </form>
    </section>
</x-layouts.app>

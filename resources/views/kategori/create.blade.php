<x-layouts.app title="Tambah Kategori">
    <section class="mx-auto flex max-w-3xl flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Tambah Kategori Baru</h2>
                <p class="text-sm text-jamu-muted">Isi detail kategori untuk memudahkan pengelolaan stok.</p>
            </div>
            <a href="{{ route('kategori.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <x-alert />

        <form action="{{ route('kategori.store') }}" method="POST" class="rounded-md border border-jamu-border bg-jamu-surface p-5">
            @csrf

            <div class="grid gap-4">
                <x-form.input name="nama_kategori" label="Nama Kategori" placeholder="Masukkan nama kategori" required />

                <label class="flex flex-col gap-1 text-sm">
                    <span class="font-medium text-jamu-text">Deskripsi</span>
                    <textarea name="deskripsi" rows="5" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm shadow-sm focus:border-jamu-secondary focus:ring-jamu-secondary" placeholder="Deskripsi kategori (opsional)">{{ old('deskripsi') }}</textarea>
                    <x-form.error name="deskripsi" />
                </label>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Simpan Kategori</button>
                <a href="{{ route('kategori.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Batal</a>
            </div>
        </form>
    </section>
</x-layouts.app>

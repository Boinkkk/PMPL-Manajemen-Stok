<x-layouts.app title="Edit Satuan">
    <section class="mx-auto flex max-w-3xl flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Edit Satuan</h2>
                <p class="text-sm text-jamu-muted">Perbarui informasi satuan produk.</p>
            </div>
            <a href="{{ route('satuan.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        <x-alert />

        <form action="{{ route('satuan.update', $satuan) }}" method="POST" class="rounded-md border border-jamu-border bg-jamu-surface p-5">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <x-form.input name="nama_satuan" label="Nama Satuan" :value="$satuan->nama_satuan" placeholder="Masukkan nama satuan" required />
                <x-form.input name="singkatan" label="Singkatan" :value="$satuan->singkatan" placeholder="Contoh: pcs, botol, dus" required />
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">Perbarui Satuan</button>
                <a href="{{ route('satuan.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Batal</a>
            </div>
        </form>
    </section>
</x-layouts.app>

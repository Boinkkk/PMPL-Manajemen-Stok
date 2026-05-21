<x-layouts.app title="Tambah Distributor">
    <section class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Tambah Distributor</h2>
                <p class="text-sm text-jamu-muted">Kode distributor ditampilkan saat form dibuka dan disimpan otomatis oleh sistem.</p>
            </div>
            <a href="{{ route('distributor.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        @include('distributor._form', [
            'action' => route('distributor.store'),
            'kodeDistributor' => $kodeDistributor,
        ])
    </section>
</x-layouts.app>

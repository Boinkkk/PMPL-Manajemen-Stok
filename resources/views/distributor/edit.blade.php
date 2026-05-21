<x-layouts.app title="Edit Distributor">
    <section class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Edit Distributor</h2>
                <p class="text-sm text-jamu-muted">Kode distributor bersifat permanen dan tidak dapat diubah.</p>
            </div>
            <a href="{{ route('distributor.show', $distributor) }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        @include('distributor._form', [
            'distributor' => $distributor,
            'action' => route('distributor.update', $distributor),
            'method' => 'PUT',
        ])
    </section>
</x-layouts.app>

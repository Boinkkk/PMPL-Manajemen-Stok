<x-layouts.app title="Edit Supplier">
    <section class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Edit Supplier</h2>
                <p class="text-sm text-jamu-muted">Kode supplier bersifat permanen dan tidak dapat diubah.</p>
            </div>
            <a href="{{ route('supplier.show', $supplier) }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        @include('supplier._form', [
            'supplier' => $supplier,
            'action' => route('supplier.update', $supplier),
            'method' => 'PUT',
        ])
    </section>
</x-layouts.app>

<x-layouts.app title="Pengaturan Stok Minimum">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Pengaturan Stok Minimum</h2>
                <p class="text-sm text-jamu-muted">Ubah ambang batas stok minimum produk. Setiap perubahan dicatat ke audit trail.</p>
            </div>
            <a href="{{ route('monitoring.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Kembali</a>
        </div>

        @if ($errors->any())
            <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-semibold">Periksa kembali input stok minimum.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('monitoring.stok-minimum.update') }}" class="flex flex-col gap-4">
            @csrf
            @method('PUT')

            <x-table>
                <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                    <tr>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Stok Terkini</th>
                        <th class="px-4 py-3 text-right">Stok Minimum Saat Ini</th>
                        <th class="px-4 py-3">Stok Minimum Baru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-jamu-border">
                    @foreach ($products as $product)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $product->nama_produk }}</td>
                            <td class="px-4 py-3">{{ $product->kategori?->nama_kategori ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">{{ $product->stok_terkini }}</td>
                            <td class="px-4 py-3 text-right">{{ $product->stok_minimum }}</td>
                            <td class="px-4 py-3">
                                <input type="number" min="0" max="10000" name="stok_minimum[{{ $product->id_produk }}]" value="{{ old('stok_minimum.'.$product->id_produk, $product->stok_minimum) }}" class="w-36 rounded-md border-jamu-border px-3 py-2 text-sm">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            <div class="flex justify-end">
                <button type="submit" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>
</x-layouts.app>

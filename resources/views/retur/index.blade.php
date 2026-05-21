<x-layouts.app title="Daftar Retur Produk">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Daftar Retur Produk</h2>
                <p class="text-sm text-jamu-muted">Kelola pengajuan retur distributor dan status persetujuan.</p>
            </div>
            <a href="{{ route('retur.create') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                Ajukan Retur
            </a>
        </div>

        <div class="grid gap-3 md:grid-cols-[1fr_auto]">
            <form action="{{ route('retur.index') }}" method="GET" class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <div class="grid gap-3 md:grid-cols-[1fr_180px_auto_auto]">
                    <input type="text" name="search" value="{{ old('search', $search) }}" placeholder="Cari produk, distributor, atau pelapor..." class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                    <select name="status" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" @selected($status === 'pending')>Pending</option>
                        <option value="disetujui" @selected($status === 'disetujui')>Disetujui</option>
                        <option value="ditolak" @selected($status === 'ditolak')>Ditolak</option>
                    </select>
                    <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">Cari</button>
                    <a href="{{ route('retur.index') }}" class="rounded-md border border-jamu-border px-4 py-2 text-center text-sm hover:bg-jamu-bg">Reset</a>
                </div>
            </form>

            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total Retur</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($returs->total(), 0, ',', '.') }}</p>
            </div>
        </div>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Distributor</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3 text-right">Jumlah</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Tanggal Lapor</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse($returs as $retur)
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3 font-medium">#{{ $retur->id_retur }}</td>
                        <td class="px-4 py-3">{{ $retur->distributor?->nama_distributor ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $retur->produk?->nama_produk ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($retur->jumlah_retur, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if($retur->status === 'pending')
                                <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">Pending</span>
                            @elseif($retur->status === 'disetujui')
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Disetujui</span>
                            @else
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-jamu-muted">{{ $retur->tanggal_lapor?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('retur.show', $retur) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Detail</a>
                                @if($retur->status === 'pending')
                                    <form action="{{ route('retur.destroy', $retur) }}" method="POST" onsubmit="return confirm('Hapus retur pending ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-jamu-muted">Belum ada retur produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $returs->links() }}
    </section>
</x-layouts.app>

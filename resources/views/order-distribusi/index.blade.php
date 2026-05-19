<x-layouts.app title="Order Distribusi">
    <section class="flex flex-col gap-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Order Distribusi</h2>
                <p class="text-sm text-jamu-muted">Kelola order distribusi ke distributor.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if (auth()->user()?->canExportOrders())
                    <a href="{{ route('order-distribusi.export', request()->query()) }}" class="rounded-md bg-jamu-green px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                        Ekspor
                    </a>
                @endif
                @if (auth()->user()?->canManageStock())
                    <a href="{{ route('order-distribusi.create') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark hover:bg-jamu-secondary-light">
                        Buat Order
                    </a>
                @endif
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-4">
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Total order hari ini</p>
                <p class="mt-2 text-2xl font-semibold">{{ $statistics['today'] }}</p>
            </div>
            <div class="rounded-md border border-yellow-200 bg-yellow-50 p-4">
                <p class="text-sm text-yellow-800">Order pending</p>
                <p class="mt-2 text-2xl font-semibold text-yellow-900">{{ $statistics['pending'] }}</p>
            </div>
            <div class="rounded-md border border-green-200 bg-green-50 p-4">
                <p class="text-sm text-green-800">Selesai bulan ini</p>
                <p class="mt-2 text-2xl font-semibold text-green-900">{{ $statistics['done_this_month'] }}</p>
            </div>
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Nilai distribusi bulan ini</p>
                <p class="mt-2 text-2xl font-semibold">Rp {{ number_format((float) $statistics['value_this_month'], 0, ',', '.') }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route('order-distribusi.index') }}" class="grid gap-3 rounded-md border border-jamu-border bg-jamu-surface p-4 md:grid-cols-6">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nomor/distributor" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">

            <select name="status" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <option value="">Semua status</option>
                @foreach (['pending', 'disetujui', 'ditolak', 'selesai'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <select name="id_distributor" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
                <option value="">Semua distributor</option>
                @foreach ($distributors as $distributor)
                    <option value="{{ $distributor->id_distributor }}" @selected(($filters['id_distributor'] ?? '') == $distributor->id_distributor)>
                        {{ $distributor->nama_distributor }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">
            <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="rounded-md border-jamu-border bg-white px-3 py-2 text-sm">

            <button type="submit" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">
                Terapkan
            </button>
        </form>

        <x-table>
            <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                <tr>
                    <th class="px-4 py-3"><a href="{{ route('order-distribusi.index', array_merge(request()->query(), ['sort' => 'nomor_order', 'direction' => (($filters['direction'] ?? 'desc') === 'asc' ? 'desc' : 'asc')])) }}">Nomor Order</a></th>
                    <th class="px-4 py-3"><a href="{{ route('order-distribusi.index', array_merge(request()->query(), ['sort' => 'distributor', 'direction' => (($filters['direction'] ?? 'desc') === 'asc' ? 'desc' : 'asc')])) }}">Distributor</a></th>
                    <th class="px-4 py-3"><a href="{{ route('order-distribusi.index', array_merge(request()->query(), ['sort' => 'tanggal_order', 'direction' => (($filters['direction'] ?? 'desc') === 'asc' ? 'desc' : 'asc')])) }}">Tanggal</a></th>
                    <th class="px-4 py-3">Diproses</th>
                    <th class="px-4 py-3 text-center">Item</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3"><a href="{{ route('order-distribusi.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => (($filters['direction'] ?? 'desc') === 'asc' ? 'desc' : 'asc')])) }}">Status</a></th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-jamu-border">
                @forelse ($orders as $order)
                    <tr class="hover:bg-jamu-bg">
                        <td class="px-4 py-3 font-medium">{{ $order->nomor_order }}</td>
                        <td class="px-4 py-3">{{ $order->distributor?->nama_distributor ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $order->tanggal_order_formatted }}</td>
                        <td class="px-4 py-3">{{ $order->tanggal_diproses_formatted ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">{{ $order->detail_orders_count }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format((float) $order->total_nilai_order, 0, ',', '.') }}</td>
                        <td class="px-4 py-3"><x-badge :status="$order->status" /></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('order-distribusi.show', $order) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Detail</a>
                                <a href="{{ route('order-distribusi.print', $order) }}" target="_blank" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Cetak</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-jamu-muted">Belum ada order distribusi.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        {{ $orders->links() }}
    </section>
</x-layouts.app>

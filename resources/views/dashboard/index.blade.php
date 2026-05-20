<x-layouts.app :title="'Dashboard '.$role">
    <section
        class="flex flex-col gap-6"
        x-data="mainDashboard()"
        x-init="init()"
    >
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Dashboard {{ $role }}</h2>
                <p class="text-sm text-jamu-muted">Ringkasan performa stok dan distribusi.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select x-model="periode" @change="refresh()" class="rounded-md border-jamu-border bg-jamu-surface px-3 py-2 text-sm">
                    <option value="bulan_ini">Bulan ini</option>
                    <option value="bulan_lalu">Bulan lalu</option>
                    <option value="3_bulan">3 bulan terakhir</option>
                    <option value="6_bulan">6 bulan terakhir</option>
                    <option value="tahun_ini">Tahun ini</option>
                    <option value="custom">Custom range</option>
                </select>
                <template x-if="periode === 'custom'">
                    <div class="flex gap-2">
                        <input type="date" x-model="tanggal_mulai" @change="refresh()" class="rounded-md border-jamu-border bg-jamu-surface px-3 py-2 text-sm">
                        <input type="date" x-model="tanggal_selesai" @change="refresh()" class="rounded-md border-jamu-border bg-jamu-surface px-3 py-2 text-sm">
                    </div>
                </template>
                @if ($role === 'Manajer')
                    <button type="button" onclick="window.print()" class="rounded-md bg-jamu-green px-4 py-2 text-sm font-semibold text-white">Ekspor Snapshot PDF</button>
                @endif
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
            <template x-for="card in visibleCards()" :key="card.key">
                <a :href="card.key === 'produk_perlu_perhatian' ? '{{ route('monitoring.index') }}' : '#'" class="rounded-md border border-jamu-border bg-jamu-surface p-4 hover:bg-jamu-bg">
                    <p class="text-sm text-jamu-muted" x-text="card.label"></p>
                    <p class="mt-2 text-2xl font-semibold" x-text="formatValue(card)"></p>
                    <p class="mt-1 text-xs" :class="card.change >= 0 ? 'text-green-700' : 'text-red-700'" x-text="`${card.change >= 0 ? 'Naik' : 'Turun'} ${Math.abs(card.change)}%`"></p>
                </a>
            </template>
        </div>

        @if ($role === 'Manajer')
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <p class="text-sm text-jamu-muted">Perbandingan nilai distribusi periode ini vs periode sebelumnya</p>
                <p class="mt-1 text-xl font-semibold" x-text="distributionComparison()"></p>
            </div>
        @endif

        <div class="grid gap-4 xl:grid-cols-2">
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <h3 class="font-semibold">Pergerakan Stok 30 Hari Terakhir</h3>
                <div class="mt-4 h-80"><canvas id="chartMovement"></canvas></div>
            </div>
            @if ($showAllCharts)
                <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                    <h3 class="font-semibold">Order Distribusi per Status</h3>
                    <div class="mt-4 h-80"><canvas id="chartOrderStatus"></canvas></div>
                </div>
                <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                    <h3 class="font-semibold">Top 5 Produk Terlaris</h3>
                    <div class="mt-4 h-80"><canvas id="chartTopProducts"></canvas></div>
                </div>
                <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                    <h3 class="font-semibold">Nilai Distribusi per Distributor</h3>
                    <div class="mt-4 h-80"><canvas id="chartDistributor"></canvas></div>
                </div>
            @endif
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <h3 class="font-semibold">5 Stok Masuk Terbaru</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-jamu-border">
                            @foreach ($activities['stok_masuk'] as $transaksi)
                                <tr class="hover:bg-jamu-bg">
                                    <td class="py-3"><a href="{{ route('stok-masuk.show', $transaksi) }}" class="font-medium">{{ $transaksi->nomor_transaksi }}</a></td>
                                    <td class="py-3">{{ $transaksi->supplier?->nama_supplier ?? '-' }}</td>
                                    <td class="py-3">{{ $transaksi->tanggal_masuk_formatted }}</td>
                                    <td class="py-3 text-right">{{ $transaksi->detail_stok_masuk_count }} item</td>
                                    <td class="py-3">{{ $transaksi->pengguna?->nama_lengkap ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <h3 class="font-semibold">5 Order Distribusi Terbaru</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-jamu-border">
                            @foreach ($activities['orders'] as $order)
                                <tr class="hover:bg-jamu-bg">
                                    <td class="py-3"><a href="{{ route('order-distribusi.show', $order->id_order) }}" class="font-medium">{{ $order->nomor_order }}</a></td>
                                    <td class="py-3">{{ $order->nama_distributor ?? '-' }}</td>
                                    <td class="py-3">{{ \Illuminate\Support\Carbon::parse($order->tanggal_order)->locale('id')->translatedFormat('d F Y') }}</td>
                                    <td class="py-3"><x-badge :status="$order->status" /></td>
                                    <td class="py-3 text-right">Rp {{ number_format((float) $order->total_nilai, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($role === 'Administrator')
            <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
                <h3 class="font-semibold">Audit Trail Terbaru</h3>
                <div class="mt-3 grid gap-2">
                    @foreach ($activities['audit'] as $audit)
                        <div class="flex justify-between gap-3 rounded-md border border-jamu-border bg-jamu-bg px-3 py-2 text-sm">
                            <span>{{ $audit->pengguna?->nama_lengkap ?? '-' }} melakukan {{ $audit->aksi }} pada {{ $audit->modul }}</span>
                            <span class="text-jamu-muted">{{ $audit->waktu_aksi?->locale('id')->translatedFormat('d F Y H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($role === 'Staf Gudang')
            <div class="rounded-md border border-yellow-200 bg-yellow-50 p-4">
                <h3 class="font-semibold text-yellow-900">Produk Perlu Perhatian</h3>
                <div class="mt-3 grid gap-2">
                    @forelse ($warningProducts as $product)
                        <div class="flex items-center justify-between gap-3 rounded-md bg-white px-3 py-2 text-sm">
                            <span class="font-medium">{{ $product->nama_produk }}</span>
                            <span class="{{ $product->stok_terkini == 0 ? 'text-red-700' : 'text-yellow-800' }}">
                                Stok {{ $product->stok_terkini }} / min {{ $product->stok_minimum }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-yellow-800">Tidak ada produk yang perlu perhatian.</p>
                    @endforelse
                </div>
            </div>
        @endif

        <div class="flex flex-wrap gap-2">
            @if ($role === 'Administrator')
                <a href="{{ route('pengguna.create') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark">Tambah Pengguna</a>
                <a href="{{ route('monitoring.products') }}" class="rounded-md border border-jamu-border px-4 py-2 text-sm hover:bg-jamu-surface">Tambah Produk</a>
            @endif
            @if ($role !== 'Manajer')
                <a href="{{ route('stok-masuk.create') }}" class="rounded-md bg-jamu-secondary px-4 py-2 text-sm font-semibold text-jamu-primary-dark">Catat Stok Masuk</a>
                <a href="{{ route('order-distribusi.create') }}" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white">Buat Order</a>
            @endif
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function mainDashboard() {
            return {
                periode: 'bulan_ini',
                tanggal_mulai: '',
                tanggal_selesai: '',
                kpi: {},
                charts: {},
                async init() {
                    await this.refresh();
                },
                params() {
                    const params = new URLSearchParams({ periode: this.periode });
                    if (this.periode === 'custom') {
                        params.set('tanggal_mulai', this.tanggal_mulai);
                        params.set('tanggal_selesai', this.tanggal_selesai);
                    }
                    return params.toString();
                },
                async refresh() {
                    const [kpi, movement, status, top, distributor] = await Promise.all([
                        fetch(`{{ route('dashboard.kpi') }}?${this.params()}`).then((r) => r.json()),
                        fetch(`{{ route('dashboard.grafik.pergerakan-stok') }}?${this.params()}`).then((r) => r.json()),
                        fetch(`{{ route('dashboard.grafik.order-status') }}?${this.params()}`).then((r) => r.json()),
                        fetch(`{{ route('dashboard.grafik.produk-terlaris') }}?${this.params()}`).then((r) => r.json()),
                        fetch(`{{ route('dashboard.grafik.distribusi-distributor') }}?${this.params()}`).then((r) => r.json()),
                    ]);
                    this.kpi = kpi.data;
                    this.drawLine('chartMovement', movement.data.labels, [
                        { label: 'Masuk', data: movement.data.masuk, borderColor: '#2563eb' },
                        { label: 'Keluar', data: movement.data.keluar, borderColor: '#dc2626' },
                    ]);
                    this.drawPie('chartOrderStatus', status.data.labels, status.data.data);
                    this.drawBar('chartTopProducts', top.data.labels, top.data.data, true);
                    this.drawBar('chartDistributor', distributor.data.labels, distributor.data.data, false);
                },
                visibleCards() {
                    const keys = ['total_produk', 'nilai_total_stok', 'order_bulan_ini', 'stok_masuk', 'stok_keluar', 'produk_perlu_perhatian', 'pengguna_aktif'];
                    return keys
                        .filter((key) => this.kpi[key])
                        .filter((key) => {{ $showInventoryValue ? 'true' : 'false' }} || key !== 'nilai_total_stok')
                        .filter((key) => '{{ $role }}' === 'Administrator' || key !== 'pengguna_aktif')
                        .map((key) => ({ key, ...this.kpi[key] }));
                },
                formatValue(card) {
                    if (card.currency) {
                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(card.value || 0);
                    }
                    return new Intl.NumberFormat('id-ID').format(card.value || 0);
                },
                distributionComparison() {
                    const item = this.kpi.nilai_distribusi_comparison || { current: 0, change: 0 };
                    const amount = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(item.current || 0);
                    return `${amount} (${item.change >= 0 ? 'naik' : 'turun'} ${Math.abs(item.change || 0)}%)`;
                },
                drawLine(id, labels, datasets) {
                    this.replaceChart(id, { type: 'line', data: { labels, datasets }, options: { responsive: true, maintainAspectRatio: false } });
                },
                drawPie(id, labels, data) {
                    this.replaceChart(id, { type: 'doughnut', data: { labels, datasets: [{ data, backgroundColor: ['#F6D78B', '#60a5fa', '#2F7D5C', '#ef4444'] }] }, options: { responsive: true, maintainAspectRatio: false } });
                },
                drawBar(id, labels, data, horizontal) {
                    this.replaceChart(id, { type: 'bar', data: { labels, datasets: [{ data, backgroundColor: '#D99A22' }] }, options: { indexAxis: horizontal ? 'y' : 'x', responsive: true, maintainAspectRatio: false } });
                },
                replaceChart(id, config) {
                    const canvas = document.getElementById(id);
                    if (! canvas || ! window.Chart) return;
                    if (this.charts[id]) this.charts[id].destroy();
                    this.charts[id] = new Chart(canvas, config);
                },
            };
        }
    </script>
</x-layouts.app>

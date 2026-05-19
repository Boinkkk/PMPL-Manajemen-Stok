<x-layouts.app title="Dashboard Monitoring">
    <section
        class="flex flex-col gap-6"
        x-data="monitoringDashboard({
            summary: @js($summary),
            chartData: @js($chartData),
            updatedAt: @js($updatedAt),
        })"
        x-init="init()"
    >
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Dashboard Monitoring</h2>
                <p class="text-sm text-jamu-muted">Pantau stok, notifikasi, dan batch mendekati kedaluwarsa.</p>
            </div>
            <p class="rounded-md border border-jamu-border bg-jamu-surface px-3 py-2 text-sm text-jamu-muted">
                Terakhir diperbarui: <span x-text="updatedAt"></span>
            </p>
        </div>

        <div class="grid gap-3 md:grid-cols-4">
            <a href="{{ route('monitoring.products') }}" class="rounded-md border border-jamu-border bg-jamu-surface p-4 hover:bg-jamu-bg">
                <p class="text-sm text-jamu-muted">Total Produk Aktif</p>
                <p class="mt-2 text-2xl font-semibold" x-text="summary.total_produk"></p>
            </a>
            <a href="#stok-menipis" class="rounded-md border border-yellow-200 bg-yellow-50 p-4 hover:bg-yellow-100">
                <p class="text-sm text-yellow-800">Produk Stok Menipis</p>
                <p class="mt-2 text-2xl font-semibold text-yellow-900" x-text="summary.stok_menipis"></p>
            </a>
            <a href="#stok-habis" class="rounded-md border border-red-200 bg-red-50 p-4 hover:bg-red-100">
                <p class="text-sm text-red-800">Produk Stok Habis</p>
                <p class="mt-2 text-2xl font-semibold text-red-900" x-text="summary.stok_habis"></p>
            </a>
            <a href="#kedaluwarsa" class="rounded-md border border-orange-200 bg-orange-50 p-4 hover:bg-orange-100">
                <p class="text-sm text-orange-800">Mendekati Kedaluwarsa</p>
                <p class="mt-2 text-2xl font-semibold text-orange-900" x-text="summary.mendekati_kedaluwarsa"></p>
            </a>
        </div>

        <div class="rounded-md border border-jamu-border bg-jamu-surface p-4">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-semibold">Pergerakan Stok 7 Hari Terakhir</h3>
                    <p class="text-sm text-jamu-muted">Total kuantitas stok masuk dan stok keluar per hari.</p>
                </div>
            </div>
            <div class="h-80">
                <canvas id="stockMovementChart"></canvas>
            </div>
        </div>

        <div id="stok-menipis" class="flex flex-col gap-3">
            <h3 class="text-lg font-semibold">Produk Stok Menipis</h3>
            <x-table>
                <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                    <tr>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Stok Saat Ini</th>
                        <th class="px-4 py-3 text-right">Stok Minimum</th>
                        <th class="px-4 py-3 text-right">Selisih</th>
                        <th class="px-4 py-3">Progress</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-jamu-border">
                    @forelse ($lowStockProducts as $product)
                        @php
                            $percent = $product->stok_minimum > 0 ? min(100, round(($product->stok_terkini / $product->stok_minimum) * 100)) : 0;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $product->nama_produk }}</td>
                            <td class="px-4 py-3">{{ $product->kategori?->nama_kategori ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">{{ $product->stok_terkini }}</td>
                            <td class="px-4 py-3 text-right">{{ $product->stok_minimum }}</td>
                            <td class="px-4 py-3 text-right">{{ max(0, $product->stok_minimum - $product->stok_terkini) }}</td>
                            <td class="px-4 py-3">
                                <div class="h-2 rounded-full bg-yellow-100">
                                    <div class="h-2 rounded-full bg-yellow-500" style="width: {{ $percent }}%"></div>
                                </div>
                                <p class="mt-1 text-xs text-jamu-muted">{{ $percent }}%</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('monitoring.products', ['q' => $product->kode_produk]) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Lihat Produk</a>
                                    @if (auth()->user()?->canManageStock())
                                        <a href="{{ route('stok-masuk.create') }}" class="rounded-md bg-jamu-secondary px-3 py-1.5 text-sm font-semibold text-jamu-primary-dark">Catat Stok Masuk</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-jamu-muted">Tidak ada produk stok menipis.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>

        <div id="stok-habis" class="flex flex-col gap-3">
            <h3 class="text-lg font-semibold">Produk Stok Habis</h3>
            <x-table>
                <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                    <tr>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Terakhir Stok Masuk</th>
                        <th class="px-4 py-3">Terakhir Stok Keluar</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-jamu-border">
                    @forelse ($outOfStockProducts as $product)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $product->nama_produk }}</td>
                            <td class="px-4 py-3">{{ $product->kategori?->nama_kategori ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $product->terakhir_stok_masuk ? \Illuminate\Support\Carbon::parse($product->terakhir_stok_masuk)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                            <td class="px-4 py-3">{{ $product->terakhir_stok_keluar ? \Illuminate\Support\Carbon::parse($product->terakhir_stok_keluar)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('monitoring.products', ['q' => $product->kode_produk]) }}" class="rounded-md border border-jamu-border px-3 py-1.5 text-sm hover:bg-jamu-bg">Lihat Produk</a>
                                    @if (auth()->user()?->canManageStock())
                                        <a href="{{ route('stok-masuk.create') }}" class="rounded-md bg-jamu-secondary px-3 py-1.5 text-sm font-semibold text-jamu-primary-dark">Catat Stok Masuk</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-jamu-muted">Tidak ada produk stok habis.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>

        <div id="kedaluwarsa" class="flex flex-col gap-3">
            <h3 class="text-lg font-semibold">Produk Mendekati Kedaluwarsa</h3>
            <x-table>
                <thead class="bg-jamu-secondary-light/40 text-left text-xs uppercase tracking-wide text-jamu-muted">
                    <tr>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Nomor Batch</th>
                        <th class="px-4 py-3">Tanggal Expired</th>
                        <th class="px-4 py-3 text-right">Sisa Hari</th>
                        <th class="px-4 py-3 text-right">Stok Batch</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-jamu-border">
                    @forelse ($expiringBatches as $batch)
                        @php
                            $daysLeft = today('Asia/Jakarta')->diffInDays($batch->tanggal_expired, false);
                            $rowClass = $daysLeft <= 7 ? 'bg-red-50' : ($daysLeft <= 14 ? 'bg-orange-50' : 'bg-yellow-50');
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="px-4 py-3 font-medium">{{ $batch->produk?->nama_produk ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $batch->nomor_batch }}</td>
                            <td class="px-4 py-3">{{ $batch->tanggal_expired?->locale('id')->translatedFormat('d F Y') }}</td>
                            <td class="px-4 py-3 text-right">{{ $daysLeft }}</td>
                            <td class="px-4 py-3 text-right">{{ (int) $batch->stok_batch }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-jamu-muted">Tidak ada batch mendekati kedaluwarsa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>

        @if (auth()->user()?->isAdministrator())
            <div class="flex justify-end">
                <a href="{{ route('monitoring.stok-minimum.index') }}" class="rounded-md bg-jamu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-jamu-primary-dark">
                    Atur Ambang Stok Minimum
                </a>
            </div>
        @endif
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function monitoringDashboard(config) {
            return {
                summary: config.summary,
                chartData: config.chartData,
                updatedAt: config.updatedAt,
                chart: null,
                init() {
                    this.renderChart();
                    setInterval(() => this.refresh(), 300000);
                },
                async refresh() {
                    const [summaryResponse, chartResponse] = await Promise.all([
                        fetch('{{ route('monitoring.summary') }}', { headers: { 'Accept': 'application/json' } }),
                        fetch('{{ route('monitoring.chart-data') }}', { headers: { 'Accept': 'application/json' } }),
                    ]);
                    const summaryJson = await summaryResponse.json();
                    const chartJson = await chartResponse.json();
                    this.summary = summaryJson.data;
                    this.updatedAt = summaryJson.data.updated_at;
                    this.chartData = chartJson.data;
                    this.renderChart();
                },
                renderChart() {
                    const canvas = document.getElementById('stockMovementChart');
                    if (! canvas || ! window.Chart) {
                        return;
                    }

                    if (this.chart) {
                        this.chart.destroy();
                    }

                    this.chart = new Chart(canvas, {
                        type: 'line',
                        data: {
                            labels: this.chartData.labels,
                            datasets: [
                                {
                                    label: 'Stok Masuk',
                                    data: this.chartData.stok_masuk,
                                    borderColor: '#2F7D5C',
                                    backgroundColor: 'rgba(47, 125, 92, 0.12)',
                                    tension: 0.35,
                                    fill: true,
                                },
                                {
                                    label: 'Stok Keluar',
                                    data: this.chartData.stok_keluar,
                                    borderColor: '#D99A22',
                                    backgroundColor: 'rgba(217, 154, 34, 0.12)',
                                    tension: 0.35,
                                    fill: true,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { precision: 0 },
                                },
                            },
                        },
                    });
                },
            };
        }
    </script>
</x-layouts.app>

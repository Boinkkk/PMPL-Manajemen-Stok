<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsService
{
    /**
     * TTL cache grafik dashboard dalam detik.
     */
    private const CACHE_TTL = 300;

    /**
     * Ambil KPI dashboard sesuai periode.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function kpi(array $filters): array
    {
        [$start, $end, $previousStart, $previousEnd] = $this->periodRange($filters);

        return Cache::remember($this->cacheKey('kpi', $filters), self::CACHE_TTL, function () use ($start, $end, $previousStart, $previousEnd): array {
            $currentOrders = $this->orderCounts($start, $end);
            $previousOrders = $this->orderCounts($previousStart, $previousEnd);

            $stokMasuk = DB::table('stok_masuk')->whereBetween('tanggal_masuk', [$start, $end])->count();
            $stokMasukPrev = DB::table('stok_masuk')->whereBetween('tanggal_masuk', [$previousStart, $previousEnd])->count();
            $stokKeluar = DB::table('stok_keluar')->whereBetween('tanggal_keluar', [$start, $end])->whereNull('deleted_at')->count();
            $stokKeluarPrev = DB::table('stok_keluar')->whereBetween('tanggal_keluar', [$previousStart, $previousEnd])->whereNull('deleted_at')->count();

            $stokHabis = DB::table('produk')->where('stok_terkini', 0)->count();
            $stokMenipis = DB::table('produk')
                ->where('stok_terkini', '>', 0)
                ->whereColumn('stok_terkini', '<=', 'stok_minimum')
                ->count();
            $expiredSoon = DB::table('batch')
                ->whereBetween('tanggal_expired', [today('Asia/Jakarta')->toDateString(), today('Asia/Jakarta')->addDays(30)->toDateString()])
                ->distinct('id_produk')
                ->count('id_produk');

            return [
                'total_produk' => [
                    'value' => DB::table('produk')->count(),
                    'label' => 'Total Produk Aktif',
                    'change' => 0,
                ],
                'nilai_total_stok' => [
                    'value' => (float) DB::table('produk')->sum(DB::raw('stok_terkini * harga_satuan')),
                    'label' => 'Nilai Total Stok',
                    'change' => 0,
                    'currency' => true,
                ],
                'order_bulan_ini' => [
                    'value' => array_sum($currentOrders),
                    'label' => 'Order Periode Ini',
                    'change' => $this->percentageChange(array_sum($previousOrders), array_sum($currentOrders)),
                    'breakdown' => $currentOrders,
                ],
                'stok_masuk' => [
                    'value' => $stokMasuk,
                    'label' => 'Transaksi Stok Masuk',
                    'change' => $this->percentageChange($stokMasukPrev, $stokMasuk),
                ],
                'stok_keluar' => [
                    'value' => $stokKeluar,
                    'label' => 'Transaksi Stok Keluar',
                    'change' => $this->percentageChange($stokKeluarPrev, $stokKeluar),
                ],
                'produk_perlu_perhatian' => [
                    'value' => $stokHabis + $stokMenipis + $expiredSoon,
                    'label' => 'Produk Perlu Perhatian',
                    'change' => 0,
                ],
                'pengguna_aktif' => [
                    'value' => DB::table('pengguna')->where('status', 'aktif')->count(),
                    'label' => 'Pengguna Aktif',
                    'change' => 0,
                ],
                'nilai_distribusi_comparison' => $this->distributionValueComparison($start, $end, $previousStart, $previousEnd),
            ];
        });
    }

    /**
     * Data grafik pergerakan stok.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function stockMovement(array $filters): array
    {
        return Cache::remember($this->cacheKey('stock-movement', $filters), self::CACHE_TTL, function () use ($filters): array {
            [$start, $end] = $this->periodRange($filters);
            $dates = collect(CarbonPeriod::create($start, $end))->map(fn ($date): CarbonImmutable => CarbonImmutable::parse($date));

            $incoming = DB::table('detail_stok_masuk')
                ->join('stok_masuk', 'stok_masuk.id_stok_masuk', '=', 'detail_stok_masuk.id_stok_masuk')
                ->whereBetween('stok_masuk.tanggal_masuk', [$start, $end])
                ->selectRaw('DATE(stok_masuk.tanggal_masuk) as tanggal, SUM(detail_stok_masuk.jumlah) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');

            $outgoing = DB::table('detail_stok_keluar')
                ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
                ->whereNull('stok_keluar.deleted_at')
                ->whereBetween('stok_keluar.tanggal_keluar', [$start, $end])
                ->selectRaw('DATE(stok_keluar.tanggal_keluar) as tanggal, SUM(detail_stok_keluar.jumlah) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');

            return [
                'labels' => $dates->map(fn (CarbonImmutable $date): string => $date->locale('id')->translatedFormat('d M'))->all(),
                'masuk' => $dates->map(fn (CarbonImmutable $date): int => (int) ($incoming[$date->toDateString()] ?? 0))->all(),
                'keluar' => $dates->map(fn (CarbonImmutable $date): int => (int) ($outgoing[$date->toDateString()] ?? 0))->all(),
            ];
        });
    }

    /**
     * Data grafik status order.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function orderStatus(array $filters): array
    {
        return Cache::remember($this->cacheKey('order-status', $filters), self::CACHE_TTL, function () use ($filters): array {
            [$start, $end] = $this->periodRange($filters);
            $counts = DB::table('order_distribusi')
                ->whereBetween('tanggal_order', [$start, $end])
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            return [
                'labels' => ['pending', 'disetujui', 'selesai', 'ditolak'],
                'data' => collect(['pending', 'disetujui', 'selesai', 'ditolak'])
                    ->map(fn (string $status): int => (int) ($counts[$status] ?? 0))
                    ->all(),
            ];
        });
    }

    /**
     * Data top produk terlaris.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function topProducts(array $filters): array
    {
        return Cache::remember($this->cacheKey('top-products', $filters), self::CACHE_TTL, function () use ($filters): array {
            [$start, $end] = $this->periodRange($filters);
            $rows = DB::table('detail_stok_keluar')
                ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
                ->join('produk', 'produk.id_produk', '=', 'detail_stok_keluar.id_produk')
                ->whereNull('stok_keluar.deleted_at')
                ->whereBetween('stok_keluar.tanggal_keluar', [$start, $end])
                ->selectRaw('produk.nama_produk, SUM(detail_stok_keluar.jumlah) as total')
                ->groupBy('produk.id_produk', 'produk.nama_produk')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            return [
                'labels' => $rows->pluck('nama_produk')->all(),
                'data' => $rows->pluck('total')->map(fn ($value): int => (int) $value)->all(),
            ];
        });
    }

    /**
     * Data nilai distribusi per distributor.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function distributorDistribution(array $filters): array
    {
        return Cache::remember($this->cacheKey('distributor-distribution', $filters), self::CACHE_TTL, function () use ($filters): array {
            [$start, $end] = $this->periodRange($filters);
            $rows = DB::table('detail_stok_keluar')
                ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
                ->leftJoin('distributor', 'distributor.id_distributor', '=', 'stok_keluar.id_distributor')
                ->whereNull('stok_keluar.deleted_at')
                ->whereBetween('stok_keluar.tanggal_keluar', [$start, $end])
                ->selectRaw('COALESCE(distributor.nama_distributor, "-") as nama_distributor, SUM(detail_stok_keluar.subtotal) as total')
                ->groupBy('distributor.id_distributor', 'distributor.nama_distributor')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            return [
                'labels' => $rows->pluck('nama_distributor')->all(),
                'data' => $rows->pluck('total')->map(fn ($value): float => (float) $value)->all(),
            ];
        });
    }

    /**
     * Data aktivitas terbaru.
     *
     * @return array<string, mixed>
     */
    public function latestActivities(): array
    {
        return Cache::remember('dashboard:latest-activities:v3', self::CACHE_TTL, fn (): array => [
            'stok_masuk' => DB::table('stok_masuk')
                ->leftJoin('supplier', 'supplier.id_supplier', '=', 'stok_masuk.id_supplier')
                ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'stok_masuk.id_pengguna')
                ->leftJoin('detail_stok_masuk', 'detail_stok_masuk.id_stok_masuk', '=', 'stok_masuk.id_stok_masuk')
                ->whereNull('stok_masuk.deleted_at')
                ->selectRaw('stok_masuk.id_stok_masuk, stok_masuk.nomor_transaksi, stok_masuk.tanggal_masuk, supplier.nama_supplier, pengguna.nama_lengkap as nama_pengguna, COUNT(detail_stok_masuk.id_detail_masuk) as jumlah_item')
                ->groupBy('stok_masuk.id_stok_masuk', 'stok_masuk.nomor_transaksi', 'stok_masuk.tanggal_masuk', 'supplier.nama_supplier', 'pengguna.nama_lengkap')
                ->orderByDesc('stok_masuk.tanggal_masuk')
                ->limit(5)
                ->get()
                ->map(fn (object $row): array => [
                    'id_stok_masuk' => (int) $row->id_stok_masuk,
                    'nomor_transaksi' => $row->nomor_transaksi,
                    'tanggal_masuk' => $row->tanggal_masuk,
                    'nama_supplier' => $row->nama_supplier,
                    'nama_pengguna' => $row->nama_pengguna,
                    'jumlah_item' => (int) $row->jumlah_item,
                ])
                ->all(),
            'orders' => DB::table('order_distribusi')
                ->leftJoin('distributor', 'distributor.id_distributor', '=', 'order_distribusi.id_distributor')
                ->leftJoin('detail_order', 'detail_order.id_order', '=', 'order_distribusi.id_order')
                ->selectRaw('order_distribusi.id_order, order_distribusi.nomor_order, order_distribusi.tanggal_order, order_distribusi.status, distributor.nama_distributor, SUM(detail_order.subtotal) as total_nilai')
                ->groupBy('order_distribusi.id_order', 'order_distribusi.nomor_order', 'order_distribusi.tanggal_order', 'order_distribusi.status', 'distributor.nama_distributor')
                ->orderByDesc('order_distribusi.tanggal_order')
                ->limit(5)
                ->get()
                ->map(fn (object $row): array => [
                    'id_order' => (int) $row->id_order,
                    'nomor_order' => $row->nomor_order,
                    'tanggal_order' => $row->tanggal_order,
                    'status' => $row->status,
                    'nama_distributor' => $row->nama_distributor,
                    'total_nilai' => (float) $row->total_nilai,
                ])
                ->all(),
            'audit' => DB::table('audit_trail')
                ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'audit_trail.id_pengguna')
                ->selectRaw('audit_trail.aksi, audit_trail.modul, audit_trail.waktu_aksi, pengguna.nama_lengkap as nama_pengguna')
                ->orderByDesc('audit_trail.waktu_aksi')
                ->limit(5)
                ->get()
                ->map(fn (object $row): array => [
                    'aksi' => $row->aksi,
                    'modul' => $row->modul,
                    'waktu_aksi' => $row->waktu_aksi,
                    'nama_pengguna' => $row->nama_pengguna,
                ])
                ->all(),
        ]);
    }

    /**
     * Bersihkan cache dashboard.
     */
    public function flushCache(): void
    {
        Cache::flush();
    }

    /**
     * Ubah filter periode menjadi rentang tanggal.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, string>
     */
    public function periodRange(array $filters): array
    {
        $periode = $filters['periode'] ?? 'bulan_ini';
        $today = now('Asia/Jakarta')->toImmutable();

        [$start, $end] = match ($periode) {
            'bulan_lalu' => [$today->subMonthNoOverflow()->startOfMonth(), $today->subMonthNoOverflow()->endOfMonth()],
            '3_bulan' => [$today->subMonthsNoOverflow(2)->startOfMonth(), $today->endOfDay()],
            '6_bulan' => [$today->subMonthsNoOverflow(5)->startOfMonth(), $today->endOfDay()],
            'tahun_ini' => [$today->startOfYear(), $today->endOfDay()],
            'custom' => [
                CarbonImmutable::parse($filters['tanggal_mulai'] ?? $today->startOfMonth()->toDateString(), 'Asia/Jakarta')->startOfDay(),
                CarbonImmutable::parse($filters['tanggal_selesai'] ?? $today->toDateString(), 'Asia/Jakarta')->endOfDay(),
            ],
            default => [$today->startOfMonth(), $today->endOfDay()],
        };

        if ($start->diffInDays($end) > 365) {
            $start = $end->subDays(365)->startOfDay();
        }

        $durationDays = max(1, $start->diffInDays($end) + 1);
        $previousEnd = $start->subDay()->endOfDay();
        $previousStart = $previousEnd->subDays($durationDays - 1)->startOfDay();

        return [
            $start->toDateString(),
            $end->toDateString(),
            $previousStart->toDateString(),
            $previousEnd->toDateString(),
        ];
    }

    /**
     * Buat key cache dari filter.
     *
     * @param  array<string, mixed>  $filters
     */
    private function cacheKey(string $name, array $filters): string
    {
        ksort($filters);

        return 'dashboard:'.$name.':'.md5(json_encode($filters));
    }

    /**
     * Hitung jumlah order per status.
     *
     * @return array<string, int>
     */
    private function orderCounts(string $start, string $end): array
    {
        $counts = DB::table('order_distribusi')
            ->whereBetween('tanggal_order', [$start, $end])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'pending' => (int) ($counts['pending'] ?? 0),
            'disetujui' => (int) ($counts['disetujui'] ?? 0),
            'selesai' => (int) ($counts['selesai'] ?? 0),
            'ditolak' => (int) ($counts['ditolak'] ?? 0),
        ];
    }

    /**
     * Hitung perubahan persentase.
     */
    private function percentageChange(float|int $previous, float|int $current): float
    {
        if ((float) $previous === 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Hitung perbandingan nilai distribusi.
     *
     * @return array<string, float>
     */
    private function distributionValueComparison(string $start, string $end, string $previousStart, string $previousEnd): array
    {
        $current = (float) DB::table('detail_stok_keluar')
            ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
            ->whereNull('stok_keluar.deleted_at')
            ->whereBetween('stok_keluar.tanggal_keluar', [$start, $end])
            ->sum('detail_stok_keluar.subtotal');

        $previous = (float) DB::table('detail_stok_keluar')
            ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
            ->whereNull('stok_keluar.deleted_at')
            ->whereBetween('stok_keluar.tanggal_keluar', [$previousStart, $previousEnd])
            ->sum('detail_stok_keluar.subtotal');

        return [
            'current' => $current,
            'previous' => $previous,
            'change' => $this->percentageChange($previous, $current),
        ];
    }
}

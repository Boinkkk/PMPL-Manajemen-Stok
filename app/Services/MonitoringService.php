<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\DetailStokKeluar;
use App\Models\DetailStokMasuk;
use App\Models\Produk;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class MonitoringService
{
    /**
     * Ambil ringkasan monitoring stok.
     *
     * @return array<string, int>
     */
    public function summary(): array
    {
        return [
            'total_produk' => Produk::query()->count(),
            'stok_menipis' => Produk::query()
                ->where('stok_terkini', '>', 0)
                ->whereColumn('stok_terkini', '<=', 'stok_minimum')
                ->count(),
            'stok_habis' => Produk::query()->where('stok_terkini', 0)->count(),
            'mendekati_kedaluwarsa' => $this->expiringBatches()
                ->pluck('id_produk')
                ->unique()
                ->count(),
        ];
    }

    /**
     * Ambil data grafik stok masuk dan keluar tujuh hari terakhir.
     *
     * @return array<string, array<int, mixed>>
     */
    public function chartData(): array
    {
        $dates = collect(range(6, 0))
            ->map(fn (int $days): CarbonImmutable => now('Asia/Jakarta')->toImmutable()->subDays($days)->startOfDay());

        $incoming = DetailStokMasuk::query()
            ->join('stok_masuk', 'stok_masuk.id_stok_masuk', '=', 'detail_stok_masuk.id_stok_masuk')
            ->whereBetween('stok_masuk.tanggal_masuk', [$dates->first()->toDateString(), $dates->last()->toDateString()])
            ->selectRaw('DATE(stok_masuk.tanggal_masuk) as tanggal, SUM(detail_stok_masuk.jumlah) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $outgoing = DetailStokKeluar::query()
            ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
            ->whereBetween('stok_keluar.tanggal_keluar', [$dates->first()->toDateString(), $dates->last()->toDateString()])
            ->selectRaw('DATE(stok_keluar.tanggal_keluar) as tanggal, SUM(detail_stok_keluar.jumlah) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        return [
            'labels' => $dates->map(fn (CarbonImmutable $date): string => $date->locale('id')->translatedFormat('d M'))->all(),
            'tanggal' => $dates->map(fn (CarbonImmutable $date): string => $date->toDateString())->all(),
            'stok_masuk' => $dates->map(fn (CarbonImmutable $date): int => (int) ($incoming[$date->toDateString()] ?? 0))->all(),
            'stok_keluar' => $dates->map(fn (CarbonImmutable $date): int => (int) ($outgoing[$date->toDateString()] ?? 0))->all(),
        ];
    }

    /**
     * Ambil produk dengan stok menipis.
     *
     * @return EloquentCollection<int, Produk>
     */
    public function lowStockProducts(): EloquentCollection
    {
        return Produk::query()
            ->with(['kategori', 'satuan'])
            ->where('stok_terkini', '>', 0)
            ->whereColumn('stok_terkini', '<=', 'stok_minimum')
            ->orderByRaw('(stok_minimum - stok_terkini) DESC')
            ->orderBy('nama_produk')
            ->get();
    }

    /**
     * Ambil produk dengan stok habis.
     *
     * @return EloquentCollection<int, Produk>
     */
    public function outOfStockProducts(): EloquentCollection
    {
        return Produk::query()
            ->with(['kategori', 'satuan'])
            ->select('produk.*')
            ->selectSub(
                DetailStokMasuk::query()
                    ->join('stok_masuk', 'stok_masuk.id_stok_masuk', '=', 'detail_stok_masuk.id_stok_masuk')
                    ->selectRaw('MAX(stok_masuk.tanggal_masuk)')
                    ->whereColumn('detail_stok_masuk.id_produk', 'produk.id_produk'),
                'terakhir_stok_masuk'
            )
            ->selectSub(
                DetailStokKeluar::query()
                    ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
                    ->selectRaw('MAX(stok_keluar.tanggal_keluar)')
                    ->whereColumn('detail_stok_keluar.id_produk', 'produk.id_produk'),
                'terakhir_stok_keluar'
            )
            ->where('stok_terkini', 0)
            ->orderBy('terakhir_stok_keluar')
            ->orderBy('nama_produk')
            ->get();
    }

    /**
     * Ambil batch yang mendekati kedaluwarsa dalam 30 hari.
     *
     * @return EloquentCollection<int, Batch>
     */
    public function expiringBatches(): EloquentCollection
    {
        return $this->batchWithStockQuery()
            ->with(['produk.kategori', 'produk.satuan'])
            ->whereBetween('tanggal_expired', [today('Asia/Jakarta')->toDateString(), today('Asia/Jakarta')->addDays(30)->toDateString()])
            ->having('stok_batch', '>', 0)
            ->orderBy('tanggal_expired')
            ->orderBy('nomor_batch')
            ->get();
    }

    /**
     * Ambil semua produk untuk halaman daftar produk monitoring.
     *
     * @return EloquentCollection<int, Produk>
     */
    public function products(?string $keyword = null): EloquentCollection
    {
        return Produk::query()
            ->with(['kategori', 'satuan'])
            ->when($keyword, function ($query, string $keyword): void {
                $query->where(function ($query) use ($keyword): void {
                    $query
                        ->where('nama_produk', 'like', "%{$keyword}%")
                        ->orWhere('kode_produk', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('nama_produk')
            ->get();
    }

    /**
     * Susun semua data awal dashboard monitoring.
     *
     * @return array<string, mixed>
     */
    public function dashboardData(): array
    {
        return [
            'summary' => $this->summary(),
            'chartData' => $this->chartData(),
            'lowStockProducts' => $this->lowStockProducts(),
            'outOfStockProducts' => $this->outOfStockProducts(),
            'expiringBatches' => $this->expiringBatches(),
            'updatedAt' => now('Asia/Jakarta')->format('H:i:s'),
        ];
    }

    /**
     * Query batch dengan stok hasil kalkulasi transaksi masuk-keluar.
     */
    public function batchWithStockQuery()
    {
        return Batch::query()
            ->select('batch.*')
            ->selectSub($this->batchStockSubquery(), 'stok_batch');
    }

    /**
     * Subquery stok batch berdasarkan detail stok masuk dan keluar.
     */
    private function batchStockSubquery(): string
    {
        return 'COALESCE((SELECT SUM(dsm.jumlah) FROM detail_stok_masuk dsm WHERE dsm.id_batch = batch.id_batch), 0)
            - COALESCE((SELECT SUM(dsk.jumlah) FROM detail_stok_keluar dsk WHERE dsk.id_batch = batch.id_batch), 0)';
    }
}

<?php

namespace App\Services;

use App\Models\Pengguna;
use App\Services\Concerns\AuditTrailTrait;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LaporanService
{
    use AuditTrailTrait;

    /**
     * Definisi metadata laporan.
     *
     * @return array<string, array<string, mixed>>
     */
    public function definitions(): array
    {
        return [
            'stok-terkini' => ['title' => 'Laporan Stok Terkini', 'group' => 'Laporan Stok', 'description' => 'Snapshot real-time stok semua produk.'],
            'mutasi-stok' => ['title' => 'Laporan Mutasi Stok', 'group' => 'Laporan Stok', 'description' => 'Pergerakan stok masuk dan keluar dalam periode tertentu.'],
            'stok-kedaluwarsa' => ['title' => 'Laporan Stok Kedaluwarsa', 'group' => 'Laporan Stok', 'description' => 'Batch yang sudah atau akan melewati tanggal kedaluwarsa.'],
            'order-distribusi' => ['title' => 'Laporan Order Distribusi', 'group' => 'Laporan Distribusi', 'description' => 'Daftar order distribusi dalam periode tertentu.'],
            'kinerja-distributor' => ['title' => 'Laporan Kinerja Distributor', 'group' => 'Laporan Distribusi', 'description' => 'Analitik performa tiap distributor.'],
            'produk-terdistribusi' => ['title' => 'Laporan Produk Terdistribusi', 'group' => 'Laporan Distribusi', 'description' => 'Produk yang keluar ke distributor.'],
            'supplier-transaksi' => ['title' => 'Laporan Transaksi Supplier', 'group' => 'Laporan Supplier', 'description' => 'Transaksi stok masuk per supplier.'],
            'kinerja-supplier' => ['title' => 'Laporan Kinerja Supplier', 'group' => 'Laporan Supplier', 'description' => 'Analitik performa pasokan supplier.'],
        ];
    }

    /**
     * Ambil definisi satu laporan.
     *
     * @return array<string, mixed>
     */
    public function definition(string $type): array
    {
        return $this->definitions()[$type] ?? throw ValidationException::withMessages([
            'laporan' => 'Jenis laporan tidak dikenal.',
        ]);
    }

    /**
     * Ambil data laporan terpaginasikan.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function report(string $type, array $filters, int $perPage = 25): array
    {
        $query = $this->query($type, $filters);
        $sort = $filters['sort'] ?? null;
        $direction = ($filters['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        if ($sort !== null && in_array($sort, $this->columns($type), true)) {
            $query->orderBy($sort, $direction);
        }

        /** @var LengthAwarePaginator $rows */
        $rows = $query->paginate($perPage)->withQueryString();

        return [
            'definition' => $this->definition($type),
            'columns' => $this->columns($type),
            'rows' => $rows,
            'summary' => $this->summary($type, $filters),
            'filters' => $filters,
        ];
    }

    /**
     * Ambil semua data laporan untuk export.
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportRows(string $type, array $filters): Collection
    {
        return $this->query($type, $filters)->limit(10000)->get();
    }

    /**
     * Catat audit trail laporan.
     *
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>|null  $extra
     */
    public function audit(string $aksi, string $type, array $filters, ?Pengguna $pengguna, ?string $ipAddress, ?array $extra = null): void
    {
        $this->simpanAuditTrail($aksi, 'Laporan', null, [
            'jenis_laporan' => $type,
            'filter' => $filters,
            ...($extra ?? []),
        ], $ipAddress, $pengguna);
    }

    /**
     * Opsi filter umum.
     *
     * @return array<string, Collection<int, object>>
     */
    public function filterOptions(): array
    {
        return [
            'categories' => DB::table('kategori')->orderBy('nama_kategori')->get(),
            'products' => DB::table('produk')->orderBy('nama_produk')->get(),
            'suppliers' => DB::table('supplier')->orderBy('nama_supplier')->get(),
            'distributors' => DB::table('distributor')->orderBy('nama_distributor')->get(),
        ];
    }

    /**
     * Query laporan berdasarkan tipe.
     *
     * @param  array<string, mixed>  $filters
     */
    private function query(string $type, array $filters): Builder
    {
        return match ($type) {
            'stok-terkini' => $this->currentStockQuery($filters),
            'mutasi-stok' => $this->stockMutationQuery($filters),
            'stok-kedaluwarsa' => $this->expiredStockQuery($filters),
            'order-distribusi' => $this->orderReportQuery($filters),
            'kinerja-distributor' => $this->distributorPerformanceQuery($filters),
            'produk-terdistribusi' => $this->distributedProductQuery($filters),
            'supplier-transaksi' => $this->supplierTransactionQuery($filters),
            'kinerja-supplier' => $this->supplierPerformanceQuery($filters),
            default => throw ValidationException::withMessages(['laporan' => 'Jenis laporan tidak dikenal.']),
        };
    }

    /**
     * Kolom laporan.
     *
     * @return array<int, string>
     */
    private function columns(string $type): array
    {
        return match ($type) {
            'stok-terkini' => ['kode_produk', 'nama_produk', 'kategori', 'satuan', 'harga_satuan', 'stok_terkini', 'stok_minimum', 'nilai_stok', 'status_stok', 'batch_terdekat_expired'],
            'mutasi-stok' => ['tanggal_transaksi', 'jenis', 'nomor_transaksi', 'produk', 'batch', 'jumlah', 'sumber_tujuan', 'dicatat_oleh'],
            'stok-kedaluwarsa' => ['nama_produk', 'kode_produk', 'nomor_batch', 'tanggal_produksi', 'tanggal_expired', 'sisa_hari', 'stok_batch', 'nilai_batch', 'status'],
            'order-distribusi' => ['nomor_order', 'distributor', 'tanggal_order', 'tanggal_diproses', 'jumlah_item', 'total_nilai_order', 'status', 'diproses_oleh'],
            'kinerja-distributor' => ['nama_distributor', 'total_order', 'order_selesai', 'order_ditolak', 'total_unit_diterima', 'total_nilai_distribusi', 'rata_rata_nilai_order', 'persentase_berhasil'],
            'produk-terdistribusi' => ['nama_produk', 'kategori', 'total_unit_keluar', 'total_nilai_keluar', 'jumlah_order', 'distributor_terbanyak'],
            'supplier-transaksi' => ['nomor_transaksi', 'tanggal_masuk', 'nama_supplier', 'jumlah_item', 'total_unit_diterima', 'total_nilai_pembelian', 'dicatat_oleh'],
            'kinerja-supplier' => ['nama_supplier', 'total_transaksi', 'total_unit_dikirim', 'total_nilai_pasokan', 'rata_rata_nilai_transaksi', 'produk_paling_sering_dipasok'],
            default => [],
        };
    }

    /**
     * Ringkasan laporan.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function summary(string $type, array $filters): array
    {
        $rows = $this->query($type, $filters)->get();

        return match ($type) {
            'stok-terkini' => [
                'Total Nilai Stok' => $rows->sum('nilai_stok'),
                'Produk Stok Habis' => $rows->where('status_stok', 'Habis')->count(),
                'Produk Stok Menipis' => $rows->where('status_stok', 'Menipis')->count(),
            ],
            'mutasi-stok' => [
                'Total Unit Masuk' => $rows->where('jenis', 'Masuk')->sum('jumlah'),
                'Total Unit Keluar' => $rows->where('jenis', 'Keluar')->sum('jumlah'),
                'Selisih' => $rows->where('jenis', 'Masuk')->sum('jumlah') - $rows->where('jenis', 'Keluar')->sum('jumlah'),
            ],
            'stok-kedaluwarsa' => [
                'Total Nilai Terancam' => $rows->sum('nilai_batch'),
            ],
            'order-distribusi' => [
                'Total Order' => $rows->count(),
                'Total Nilai Order Selesai' => $rows->where('status', 'selesai')->sum('total_nilai_order'),
                'Order Ditolak (%)' => $rows->count() > 0 ? round(($rows->where('status', 'ditolak')->count() / $rows->count()) * 100, 2) : 0,
            ],
            default => [
                'Jumlah Data' => $rows->count(),
            ],
        };
    }

    /**
     * Query stok terkini.
     *
     * @param  array<string, mixed>  $filters
     */
    private function currentStockQuery(array $filters): Builder
    {
        $nearestBatch = DB::table('batch')
            ->selectRaw("CONCAT(nomor_batch, ' - ', DATE_FORMAT(tanggal_expired, '%d-%m-%Y'))")
            ->whereColumn('batch.id_produk', 'produk.id_produk')
            ->whereDate('tanggal_expired', '>=', today('Asia/Jakarta')->toDateString())
            ->orderBy('tanggal_expired')
            ->limit(1);

        return DB::table('produk')
            ->leftJoin('kategori', 'kategori.id_kategori', '=', 'produk.id_kategori')
            ->leftJoin('satuan', 'satuan.id_satuan', '=', 'produk.id_satuan')
            ->selectRaw('produk.kode_produk, produk.nama_produk, kategori.nama_kategori as kategori, satuan.nama_satuan as satuan, produk.harga_satuan, produk.stok_terkini, produk.stok_minimum, (produk.stok_terkini * produk.harga_satuan) as nilai_stok')
            ->selectSub($nearestBatch, 'batch_terdekat_expired')
            ->selectRaw("CASE WHEN produk.stok_terkini = 0 THEN 'Habis' WHEN produk.stok_terkini <= produk.stok_minimum THEN 'Menipis' ELSE 'Normal' END as status_stok")
            ->when($filters['id_kategori'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('produk.id_kategori', $id))
            ->when($filters['status_stok'] ?? null, function (Builder $query, string $status): void {
                match ($status) {
                    'habis' => $query->where('produk.stok_terkini', 0),
                    'menipis' => $query->where('produk.stok_terkini', '>', 0)->whereColumn('produk.stok_terkini', '<=', 'produk.stok_minimum'),
                    'normal' => $query->whereColumn('produk.stok_terkini', '>', 'produk.stok_minimum'),
                    default => null,
                };
            })
            ->when($filters['q'] ?? null, function (Builder $query, string $keyword): void {
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->where('produk.nama_produk', 'like', "%{$keyword}%")
                        ->orWhere('produk.kode_produk', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('produk.nama_produk');
    }

    /**
     * Query mutasi stok.
     *
     * @param  array<string, mixed>  $filters
     */
    private function stockMutationQuery(array $filters): Builder
    {
        [$start, $end] = $this->dateRange($filters);

        $incoming = DB::table('detail_stok_masuk')
            ->join('stok_masuk', 'stok_masuk.id_stok_masuk', '=', 'detail_stok_masuk.id_stok_masuk')
            ->join('produk', 'produk.id_produk', '=', 'detail_stok_masuk.id_produk')
            ->leftJoin('kategori', 'kategori.id_kategori', '=', 'produk.id_kategori')
            ->leftJoin('batch', 'batch.id_batch', '=', 'detail_stok_masuk.id_batch')
            ->leftJoin('supplier', 'supplier.id_supplier', '=', 'stok_masuk.id_supplier')
            ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'stok_masuk.id_pengguna')
            ->whereBetween('stok_masuk.tanggal_masuk', [$start, $end])
            ->selectRaw("stok_masuk.tanggal_masuk as tanggal_transaksi, 'Masuk' as jenis, stok_masuk.nomor_transaksi, produk.nama_produk as produk, batch.nomor_batch as batch, detail_stok_masuk.jumlah, supplier.nama_supplier as sumber_tujuan, pengguna.nama_lengkap as dicatat_oleh, produk.id_produk, produk.id_kategori");

        $outgoing = DB::table('detail_stok_keluar')
            ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
            ->join('produk', 'produk.id_produk', '=', 'detail_stok_keluar.id_produk')
            ->leftJoin('kategori', 'kategori.id_kategori', '=', 'produk.id_kategori')
            ->leftJoin('batch', 'batch.id_batch', '=', 'detail_stok_keluar.id_batch')
            ->leftJoin('distributor', 'distributor.id_distributor', '=', 'stok_keluar.id_distributor')
            ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'stok_keluar.id_pengguna')
            ->whereNull('stok_keluar.deleted_at')
            ->whereBetween('stok_keluar.tanggal_keluar', [$start, $end])
            ->selectRaw("stok_keluar.tanggal_keluar as tanggal_transaksi, 'Keluar' as jenis, stok_keluar.nomor_transaksi, produk.nama_produk as produk, batch.nomor_batch as batch, detail_stok_keluar.jumlah, distributor.nama_distributor as sumber_tujuan, pengguna.nama_lengkap as dicatat_oleh, produk.id_produk, produk.id_kategori");

        $query = match ($filters['jenis_mutasi'] ?? 'semua') {
            'masuk' => $incoming,
            'keluar' => $outgoing,
            default => $incoming->unionAll($outgoing),
        };

        return DB::query()
            ->fromSub($query, 'mutasi')
            ->when($filters['id_produk'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('id_produk', $id))
            ->when($filters['id_kategori'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('id_kategori', $id))
            ->orderByDesc('tanggal_transaksi');
    }

    /**
     * Query stok kedaluwarsa.
     *
     * @param  array<string, mixed>  $filters
     */
    private function expiredStockQuery(array $filters): Builder
    {
        [$start, $end] = $this->expiryRange($filters);

        return DB::table('batch')
            ->join('produk', 'produk.id_produk', '=', 'batch.id_produk')
            ->leftJoin('kategori', 'kategori.id_kategori', '=', 'produk.id_kategori')
            ->selectRaw('produk.nama_produk, produk.kode_produk, kategori.nama_kategori as kategori, batch.nomor_batch, batch.tanggal_produksi, batch.tanggal_expired, DATEDIFF(batch.tanggal_expired, CURDATE()) as sisa_hari')
            ->selectRaw('('.$this->batchStockSql().') as stok_batch')
            ->selectRaw('(('.$this->batchStockSql().') * produk.harga_satuan) as nilai_batch')
            ->selectRaw("CASE WHEN batch.tanggal_expired < CURDATE() THEN 'Sudah Expired' WHEN DATEDIFF(batch.tanggal_expired, CURDATE()) <= 7 THEN 'Kritis' WHEN DATEDIFF(batch.tanggal_expired, CURDATE()) <= 30 THEN 'Segera' ELSE 'Mendekati' END as status")
            ->whereBetween('batch.tanggal_expired', [$start, $end])
            ->when($filters['id_kategori'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('produk.id_kategori', $id))
            ->having('stok_batch', '>', 0)
            ->orderBy('batch.tanggal_expired');
    }

    /**
     * Query order distribusi.
     *
     * @param  array<string, mixed>  $filters
     */
    private function orderReportQuery(array $filters): Builder
    {
        [$start, $end] = $this->dateRange($filters, 'tanggal_order');

        return DB::table('order_distribusi')
            ->leftJoin('distributor', 'distributor.id_distributor', '=', 'order_distribusi.id_distributor')
            ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'order_distribusi.id_pengguna')
            ->leftJoin('detail_order', 'detail_order.id_order', '=', 'order_distribusi.id_order')
            ->whereBetween('order_distribusi.tanggal_order', [$start, $end])
            ->when($filters['id_distributor'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('order_distribusi.id_distributor', $id))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('order_distribusi.status', $status))
            ->selectRaw('order_distribusi.nomor_order, distributor.nama_distributor as distributor, order_distribusi.tanggal_order, order_distribusi.tanggal_diproses, COUNT(detail_order.id_detail_order) as jumlah_item, SUM(detail_order.subtotal) as total_nilai_order, order_distribusi.status, pengguna.nama_lengkap as diproses_oleh')
            ->groupBy('order_distribusi.id_order', 'order_distribusi.nomor_order', 'distributor.nama_distributor', 'order_distribusi.tanggal_order', 'order_distribusi.tanggal_diproses', 'order_distribusi.status', 'pengguna.nama_lengkap')
            ->orderByDesc('order_distribusi.tanggal_order');
    }

    /**
     * Query kinerja distributor.
     *
     * @param  array<string, mixed>  $filters
     */
    private function distributorPerformanceQuery(array $filters): Builder
    {
        [$start, $end] = $this->dateRange($filters);

        $orderAgg = DB::table('order_distribusi')
            ->leftJoin('detail_order', 'detail_order.id_order', '=', 'order_distribusi.id_order')
            ->whereBetween('order_distribusi.tanggal_order', [$start, $end])
            ->selectRaw('order_distribusi.id_distributor, COUNT(DISTINCT order_distribusi.id_order) as total_order, SUM(order_distribusi.status = "selesai") as order_selesai, SUM(order_distribusi.status = "ditolak") as order_ditolak, SUM(detail_order.subtotal) as total_nilai_order')
            ->groupBy('order_distribusi.id_distributor');

        $stokAgg = DB::table('stok_keluar')
            ->join('detail_stok_keluar', 'detail_stok_keluar.id_stok_keluar', '=', 'stok_keluar.id_stok_keluar')
            ->whereNull('stok_keluar.deleted_at')
            ->whereBetween('stok_keluar.tanggal_keluar', [$start, $end])
            ->selectRaw('stok_keluar.id_distributor, SUM(detail_stok_keluar.jumlah) as total_unit_diterima, SUM(detail_stok_keluar.subtotal) as total_nilai_distribusi')
            ->groupBy('stok_keluar.id_distributor');

        return DB::table('distributor')
            ->leftJoinSub($orderAgg, 'orders', 'orders.id_distributor', '=', 'distributor.id_distributor')
            ->leftJoinSub($stokAgg, 'stok', 'stok.id_distributor', '=', 'distributor.id_distributor')
            ->when($filters['id_distributor'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('distributor.id_distributor', $id))
            ->selectRaw('distributor.nama_distributor, COALESCE(orders.total_order, 0) as total_order, COALESCE(orders.order_selesai, 0) as order_selesai, COALESCE(orders.order_ditolak, 0) as order_ditolak, COALESCE(stok.total_unit_diterima, 0) as total_unit_diterima, COALESCE(stok.total_nilai_distribusi, 0) as total_nilai_distribusi')
            ->selectRaw('CASE WHEN COALESCE(orders.total_order, 0) = 0 THEN 0 ELSE COALESCE(stok.total_nilai_distribusi, 0) / orders.total_order END as rata_rata_nilai_order')
            ->selectRaw('CASE WHEN COALESCE(orders.total_order, 0) = 0 THEN 0 ELSE (COALESCE(orders.order_selesai, 0) / orders.total_order) * 100 END as persentase_berhasil')
            ->orderByDesc('total_nilai_distribusi');
    }

    /**
     * Query produk terdistribusi.
     *
     * @param  array<string, mixed>  $filters
     */
    private function distributedProductQuery(array $filters): Builder
    {
        [$start, $end] = $this->dateRange($filters);

        return DB::table('detail_stok_keluar')
            ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
            ->join('produk', 'produk.id_produk', '=', 'detail_stok_keluar.id_produk')
            ->leftJoin('kategori', 'kategori.id_kategori', '=', 'produk.id_kategori')
            ->leftJoin('distributor', 'distributor.id_distributor', '=', 'stok_keluar.id_distributor')
            ->whereNull('stok_keluar.deleted_at')
            ->whereBetween('stok_keluar.tanggal_keluar', [$start, $end])
            ->when($filters['id_produk'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('produk.id_produk', $id))
            ->when($filters['id_kategori'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('produk.id_kategori', $id))
            ->when($filters['id_distributor'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('stok_keluar.id_distributor', $id))
            ->selectRaw('produk.nama_produk, kategori.nama_kategori as kategori, SUM(detail_stok_keluar.jumlah) as total_unit_keluar, SUM(detail_stok_keluar.subtotal) as total_nilai_keluar, COUNT(DISTINCT stok_keluar.id_order) as jumlah_order, SUBSTRING_INDEX(GROUP_CONCAT(distributor.nama_distributor ORDER BY detail_stok_keluar.jumlah DESC SEPARATOR ","), ",", 1) as distributor_terbanyak')
            ->groupBy('produk.id_produk', 'produk.nama_produk', 'kategori.nama_kategori')
            ->orderByDesc('total_unit_keluar');
    }

    /**
     * Query transaksi supplier.
     *
     * @param  array<string, mixed>  $filters
     */
    private function supplierTransactionQuery(array $filters): Builder
    {
        [$start, $end] = $this->dateRange($filters, 'tanggal_masuk');

        return DB::table('stok_masuk')
            ->leftJoin('supplier', 'supplier.id_supplier', '=', 'stok_masuk.id_supplier')
            ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'stok_masuk.id_pengguna')
            ->leftJoin('detail_stok_masuk', 'detail_stok_masuk.id_stok_masuk', '=', 'stok_masuk.id_stok_masuk')
            ->whereNull('stok_masuk.deleted_at')
            ->whereBetween('stok_masuk.tanggal_masuk', [$start, $end])
            ->when($filters['id_supplier'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('stok_masuk.id_supplier', $id))
            ->when($filters['id_produk'] ?? null, fn (Builder $query, mixed $id): Builder => $query->where('detail_stok_masuk.id_produk', $id))
            ->selectRaw('stok_masuk.nomor_transaksi, stok_masuk.tanggal_masuk, supplier.nama_supplier, COUNT(DISTINCT detail_stok_masuk.id_produk) as jumlah_item, SUM(detail_stok_masuk.jumlah) as total_unit_diterima, SUM(detail_stok_masuk.subtotal) as total_nilai_pembelian, pengguna.nama_lengkap as dicatat_oleh')
            ->groupBy('stok_masuk.id_stok_masuk', 'stok_masuk.nomor_transaksi', 'stok_masuk.tanggal_masuk', 'supplier.nama_supplier', 'pengguna.nama_lengkap')
            ->orderByDesc('stok_masuk.tanggal_masuk');
    }

    /**
     * Query kinerja supplier.
     *
     * @param  array<string, mixed>  $filters
     */
    private function supplierPerformanceQuery(array $filters): Builder
    {
        [$start, $end] = $this->dateRange($filters, 'tanggal_masuk');

        return DB::table('supplier')
            ->leftJoin('stok_masuk', function ($join) use ($start, $end): void {
                $join->on('stok_masuk.id_supplier', '=', 'supplier.id_supplier')
                    ->whereBetween('stok_masuk.tanggal_masuk', [$start, $end])
                    ->whereNull('stok_masuk.deleted_at');
            })
            ->leftJoin('detail_stok_masuk', 'detail_stok_masuk.id_stok_masuk', '=', 'stok_masuk.id_stok_masuk')
            ->leftJoin('produk', 'produk.id_produk', '=', 'detail_stok_masuk.id_produk')
            ->selectRaw('supplier.nama_supplier, COUNT(DISTINCT stok_masuk.id_stok_masuk) as total_transaksi, COALESCE(SUM(detail_stok_masuk.jumlah), 0) as total_unit_dikirim, COALESCE(SUM(detail_stok_masuk.subtotal), 0) as total_nilai_pasokan')
            ->selectRaw('CASE WHEN COUNT(DISTINCT stok_masuk.id_stok_masuk) = 0 THEN 0 ELSE COALESCE(SUM(detail_stok_masuk.subtotal), 0) / COUNT(DISTINCT stok_masuk.id_stok_masuk) END as rata_rata_nilai_transaksi')
            ->selectRaw('SUBSTRING_INDEX(GROUP_CONCAT(produk.nama_produk ORDER BY detail_stok_masuk.jumlah DESC SEPARATOR ","), ",", 1) as produk_paling_sering_dipasok')
            ->groupBy('supplier.id_supplier', 'supplier.nama_supplier')
            ->orderByDesc('total_nilai_pasokan');
    }

    /**
     * Ambil rentang tanggal dengan batas maksimal 365 hari.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, string>
     */
    private function dateRange(array $filters, string $field = 'tanggal'): array
    {
        $start = CarbonImmutable::parse($filters['tanggal_mulai'] ?? now('Asia/Jakarta')->startOfMonth()->toDateString(), 'Asia/Jakarta')->startOfDay();
        $end = CarbonImmutable::parse($filters['tanggal_selesai'] ?? now('Asia/Jakarta')->toDateString(), 'Asia/Jakarta')->endOfDay();

        if ($start->diffInDays($end) > 365) {
            throw ValidationException::withMessages([
                $field => 'Rentang tanggal laporan maksimal 365 hari.',
            ]);
        }

        return [$start->toDateString(), $end->toDateString()];
    }

    /**
     * Ambil rentang tanggal expired dari filter kondisi.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, string>
     */
    private function expiryRange(array $filters): array
    {
        $today = today('Asia/Jakarta');

        return match ($filters['kondisi'] ?? '30_hari') {
            'expired' => ['1900-01-01', $today->subDay()->toDateString()],
            '7_hari' => [$today->toDateString(), $today->addDays(7)->toDateString()],
            '60_hari' => [$today->toDateString(), $today->addDays(60)->toDateString()],
            'custom' => $this->dateRange($filters, 'tanggal_expired'),
            default => [$today->toDateString(), $today->addDays(30)->toDateString()],
        };
    }

    /**
     * SQL stok batch berdasarkan transaksi masuk-keluar.
     */
    private function batchStockSql(): string
    {
        return 'COALESCE((SELECT SUM(dsm.jumlah) FROM detail_stok_masuk dsm WHERE dsm.id_batch = batch.id_batch), 0)
            - COALESCE((SELECT SUM(dsk.jumlah) FROM detail_stok_keluar dsk WHERE dsk.id_batch = batch.id_batch), 0)';
    }
}

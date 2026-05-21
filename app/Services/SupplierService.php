<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\Pengguna;
use App\Models\Supplier;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    /**
     * Kolom supplier yang dicatat di audit trail.
     *
     * @return array<int, string>
     */
    public function auditedColumns(): array
    {
        return [
            'id_supplier',
            'kode_supplier',
            'nama_supplier',
            'alamat',
            'telepon',
            'email',
            'kontak_person',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Query daftar supplier beserta statistik pasokan.
     *
     * @param  array<string, mixed>  $filters
     */
    public function listQuery(array $filters): Builder
    {
        $query = Supplier::query()
            ->select('supplier.*')
            ->selectSub(function ($query): void {
                $query->from('stok_masuk')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('stok_masuk.id_supplier', 'supplier.id_supplier')
                    ->whereNull('stok_masuk.deleted_at');
            }, 'total_transaksi')
            ->selectSub(function ($query): void {
                $query->from('stok_masuk')
                    ->join('detail_stok_masuk', 'detail_stok_masuk.id_stok_masuk', '=', 'stok_masuk.id_stok_masuk')
                    ->selectRaw('COALESCE(SUM(detail_stok_masuk.subtotal), 0)')
                    ->whereColumn('stok_masuk.id_supplier', 'supplier.id_supplier')
                    ->whereNull('stok_masuk.deleted_at');
            }, 'total_nilai_pasokan')
            ->selectSub(function ($query): void {
                $query->from('stok_masuk')
                    ->selectRaw('MAX(tanggal_masuk)')
                    ->whereColumn('stok_masuk.id_supplier', 'supplier.id_supplier')
                    ->whereNull('stok_masuk.deleted_at');
            }, 'tanggal_transaksi_terakhir')
            ->search($filters['q'] ?? null);

        match ($filters['sort'] ?? 'nama_asc') {
            'nama_desc' => $query->orderByDesc('nama_supplier'),
            'transaksi_desc' => $query->orderByDesc('total_transaksi')->orderBy('nama_supplier'),
            'nilai_desc' => $query->orderByDesc('total_nilai_pasokan')->orderBy('nama_supplier'),
            'terbaru' => $query->orderByDesc('created_at'),
            default => $query->orderBy('nama_supplier'),
        };

        return $query;
    }

    /**
     * Ambil daftar supplier untuk halaman index.
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->listQuery($filters)->paginate($perPage)->withQueryString();
    }

    /**
     * Ringkasan statistik supplier.
     *
     * @return array<string, float|int>
     */
    public function summary(): array
    {
        $threeMonthsAgo = now('Asia/Jakarta')->subMonthsNoOverflow(3)->toDateString();
        $activeSupplierIds = DB::table('stok_masuk')
            ->whereNull('deleted_at')
            ->whereDate('tanggal_masuk', '>=', $threeMonthsAgo)
            ->distinct()
            ->pluck('id_supplier')
            ->filter();

        $totalSupplier = Supplier::count();
        $totalNilaiBulanIni = (float) DB::table('stok_masuk')
            ->join('detail_stok_masuk', 'detail_stok_masuk.id_stok_masuk', '=', 'stok_masuk.id_stok_masuk')
            ->whereNull('stok_masuk.deleted_at')
            ->whereBetween('stok_masuk.tanggal_masuk', [
                now('Asia/Jakarta')->startOfMonth()->toDateString(),
                now('Asia/Jakarta')->endOfMonth()->toDateString(),
            ])
            ->sum('detail_stok_masuk.subtotal');

        return [
            'total_supplier' => $totalSupplier,
            'supplier_aktif' => $activeSupplierIds->count(),
            'supplier_tidak_aktif' => max(0, $totalSupplier - $activeSupplierIds->count()),
            'total_nilai_bulan_ini' => $totalNilaiBulanIni,
        ];
    }

    /**
     * Generate kode supplier berikutnya.
     */
    public function generateKodeSupplier(): string
    {
        $lastNumber = (int) DB::table('supplier')
            ->selectRaw('MAX(CAST(SUBSTRING(kode_supplier, 5) AS UNSIGNED)) as nomor_terakhir')
            ->value('nomor_terakhir');

        return sprintf('SUP-%03d', $lastNumber + 1);
    }

    /**
     * Simpan supplier baru.
     *
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, Pengguna $pengguna, ?string $ipAddress): Supplier
    {
        return DB::transaction(function () use ($data, $pengguna, $ipAddress): Supplier {
            $supplier = Supplier::create([
                'kode_supplier' => $this->generateKodeSupplierForUpdate(),
                'nama_supplier' => $data['nama_supplier'],
                'alamat' => $data['alamat'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'email' => $data['email'] ?? null,
                'kontak_person' => $data['kontak_person'] ?? null,
            ]);

            $this->audit('TAMBAH_SUPPLIER', $pengguna, $ipAddress, null, $supplier->only($this->auditedColumns()));

            return $supplier;
        });
    }

    /**
     * Perbarui supplier.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Supplier $supplier, array $data, Pengguna $pengguna, ?string $ipAddress): Supplier
    {
        return DB::transaction(function () use ($supplier, $data, $pengguna, $ipAddress): Supplier {
            $dataLama = $supplier->only($this->auditedColumns());

            $supplier->update([
                'nama_supplier' => $data['nama_supplier'],
                'alamat' => $data['alamat'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'email' => $data['email'] ?? null,
                'kontak_person' => $data['kontak_person'] ?? null,
            ]);

            $supplier->refresh();
            $this->audit('UBAH_SUPPLIER', $pengguna, $ipAddress, $dataLama, $supplier->only($this->auditedColumns()));

            return $supplier;
        });
    }

    /**
     * Hapus supplier jika belum punya transaksi.
     */
    public function destroy(Supplier $supplier, Pengguna $pengguna, ?string $ipAddress): bool
    {
        if ($this->hasTransactions($supplier)) {
            return false;
        }

        DB::transaction(function () use ($supplier, $pengguna, $ipAddress): void {
            $dataLama = $supplier->only($this->auditedColumns());
            $supplier->delete();
            $this->audit('HAPUS_SUPPLIER', $pengguna, $ipAddress, $dataLama, null);
        });

        return true;
    }

    /**
     * Cek apakah supplier punya transaksi stok masuk.
     */
    public function hasTransactions(Supplier $supplier): bool
    {
        return DB::table('stok_masuk')
            ->where('id_supplier', $supplier->id_supplier)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Statistik detail supplier.
     *
     * @return array<string, mixed>
     */
    public function supplierStats(Supplier $supplier): array
    {
        $base = DB::table('stok_masuk')
            ->leftJoin('detail_stok_masuk', 'detail_stok_masuk.id_stok_masuk', '=', 'stok_masuk.id_stok_masuk')
            ->where('stok_masuk.id_supplier', $supplier->id_supplier)
            ->whereNull('stok_masuk.deleted_at');

        $latest = DB::table('stok_masuk')
            ->where('id_supplier', $supplier->id_supplier)
            ->whereNull('deleted_at')
            ->orderByDesc('tanggal_masuk')
            ->first(['nomor_transaksi', 'tanggal_masuk']);

        $topProduct = DB::table('detail_stok_masuk')
            ->join('stok_masuk', 'stok_masuk.id_stok_masuk', '=', 'detail_stok_masuk.id_stok_masuk')
            ->join('produk', 'produk.id_produk', '=', 'detail_stok_masuk.id_produk')
            ->where('stok_masuk.id_supplier', $supplier->id_supplier)
            ->whereNull('stok_masuk.deleted_at')
            ->selectRaw('produk.nama_produk, SUM(detail_stok_masuk.jumlah) as total_unit')
            ->groupBy('produk.id_produk', 'produk.nama_produk')
            ->orderByDesc('total_unit')
            ->first();

        return [
            'total_transaksi' => (clone $base)->distinct('stok_masuk.id_stok_masuk')->count('stok_masuk.id_stok_masuk'),
            'total_unit' => (int) (clone $base)->sum('detail_stok_masuk.jumlah'),
            'total_nilai' => (float) (clone $base)->sum('detail_stok_masuk.subtotal'),
            'transaksi_terakhir' => $latest,
            'produk_tersering' => $topProduct?->nama_produk,
        ];
    }

    /**
     * Riwayat transaksi stok masuk supplier.
     *
     * @param  array<string, mixed>  $filters
     */
    public function transactionHistory(Supplier $supplier, array $filters): LengthAwarePaginator
    {
        return DB::table('stok_masuk')
            ->leftJoin('detail_stok_masuk', 'detail_stok_masuk.id_stok_masuk', '=', 'stok_masuk.id_stok_masuk')
            ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'stok_masuk.id_pengguna')
            ->where('stok_masuk.id_supplier', $supplier->id_supplier)
            ->whereNull('stok_masuk.deleted_at')
            ->when($filters['tanggal_mulai'] ?? null, fn ($query, string $date) => $query->whereDate('stok_masuk.tanggal_masuk', '>=', $date))
            ->when($filters['tanggal_selesai'] ?? null, fn ($query, string $date) => $query->whereDate('stok_masuk.tanggal_masuk', '<=', $date))
            ->when($filters['q_transaksi'] ?? null, fn ($query, string $keyword) => $query->where('stok_masuk.nomor_transaksi', 'like', "%{$keyword}%"))
            ->selectRaw('stok_masuk.id_stok_masuk, stok_masuk.nomor_transaksi, stok_masuk.tanggal_masuk, COUNT(DISTINCT detail_stok_masuk.id_produk) as jumlah_item, COALESCE(SUM(detail_stok_masuk.jumlah), 0) as total_unit, COALESCE(SUM(detail_stok_masuk.subtotal), 0) as total_nilai, pengguna.nama_lengkap as dicatat_oleh')
            ->groupBy('stok_masuk.id_stok_masuk', 'stok_masuk.nomor_transaksi', 'stok_masuk.tanggal_masuk', 'pengguna.nama_lengkap')
            ->orderByDesc('stok_masuk.tanggal_masuk')
            ->paginate(10, ['*'], 'riwayat_page')
            ->withQueryString();
    }

    /**
     * Produk yang pernah dipasok supplier.
     */
    public function suppliedProducts(Supplier $supplier): Collection
    {
        return DB::table('detail_stok_masuk')
            ->join('stok_masuk', 'stok_masuk.id_stok_masuk', '=', 'detail_stok_masuk.id_stok_masuk')
            ->join('produk', 'produk.id_produk', '=', 'detail_stok_masuk.id_produk')
            ->leftJoin('kategori', 'kategori.id_kategori', '=', 'produk.id_kategori')
            ->where('stok_masuk.id_supplier', $supplier->id_supplier)
            ->whereNull('stok_masuk.deleted_at')
            ->selectRaw('produk.id_produk, produk.nama_produk, kategori.nama_kategori, SUM(detail_stok_masuk.jumlah) as total_unit, MAX(stok_masuk.tanggal_masuk) as terakhir_dipasok, produk.stok_terkini')
            ->groupBy('produk.id_produk', 'produk.nama_produk', 'kategori.nama_kategori', 'produk.stok_terkini')
            ->orderByDesc('total_unit')
            ->get();
    }

    /**
     * Ambil pembuat supplier dari audit trail jika tersedia.
     */
    public function creatorName(Supplier $supplier): ?string
    {
        return DB::table('audit_trail')
            ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'audit_trail.id_pengguna')
            ->where('audit_trail.aksi', 'TAMBAH_SUPPLIER')
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(audit_trail.data_baru, "$.id_supplier")) = ?', [(string) $supplier->id_supplier])
            ->orderBy('audit_trail.waktu_aksi')
            ->value('pengguna.nama_lengkap');
    }

    /**
     * Cek duplikasi untuk AJAX.
     *
     * @return array<string, bool>
     */
    public function duplicateStatus(?string $namaSupplier, ?string $email, ?int $ignoreId = null): array
    {
        $nameQuery = DB::table('supplier');
        $emailQuery = DB::table('supplier');

        if ($ignoreId) {
            $nameQuery->where('id_supplier', '!=', $ignoreId);
            $emailQuery->where('id_supplier', '!=', $ignoreId);
        }

        return [
            'nama_exists' => $namaSupplier
                ? $nameQuery->whereRaw('LOWER(nama_supplier) = ?', [mb_strtolower($namaSupplier)])->exists()
                : false,
            'email_exists' => $email
                ? $emailQuery->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])->exists()
                : false,
        ];
    }

    /**
     * Data export supplier.
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportRows(array $filters): Collection
    {
        return $this->listQuery($filters)->get();
    }

    /**
     * Catat audit export supplier.
     *
     * @param  array<string, mixed>  $filters
     */
    public function auditExport(array $filters, int $jumlahData, Pengguna $pengguna, ?string $ipAddress): void
    {
        $this->audit('EKSPOR_SUPPLIER', $pengguna, $ipAddress, null, [
            'filter' => $filters,
            'jumlah_data' => $jumlahData,
        ]);
    }

    /**
     * Format rupiah.
     */
    public function rupiah(float|int|null $value): string
    {
        return 'Rp '.number_format((float) $value, 0, ',', '.');
    }

    /**
     * Format tanggal Indonesia.
     */
    public function tanggal(?string $date): string
    {
        if (! $date) {
            return '-';
        }

        return CarbonImmutable::parse($date)->locale('id')->translatedFormat('d F Y');
    }

    /**
     * Catat audit trail modul supplier.
     *
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    private function audit(string $aksi, Pengguna $pengguna, ?string $ipAddress, ?array $dataLama = null, ?array $dataBaru = null): void
    {
        AuditTrail::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'aksi' => $aksi,
            'modul' => 'Supplier',
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => $ipAddress,
            'waktu_aksi' => now('Asia/Jakarta'),
        ]);
    }

    /**
     * Generate kode supplier sambil mengunci tabel untuk proses simpan.
     */
    private function generateKodeSupplierForUpdate(): string
    {
        $lastCode = DB::table('supplier')
            ->lockForUpdate()
            ->orderByDesc('id_supplier')
            ->value('kode_supplier');

        $lastNumber = $lastCode ? (int) substr((string) $lastCode, 4) : 0;

        return sprintf('SUP-%03d', $lastNumber + 1);
    }
}

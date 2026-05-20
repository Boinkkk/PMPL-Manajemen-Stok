<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\Distributor;
use App\Models\Pengguna;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DistributorService
{
    /**
     * Kolom distributor yang dicatat di audit trail.
     *
     * @return array<int, string>
     */
    public function auditedColumns(): array
    {
        return [
            'id_distributor',
            'kode_distributor',
            'nama_distributor',
            'alamat',
            'telepon',
            'email',
            'kontak_person',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Query daftar distributor beserta statistik distribusi.
     *
     * @param  array<string, mixed>  $filters
     */
    public function listQuery(array $filters): Builder
    {
        $query = Distributor::query()
            ->select('distributor.*')
            ->selectSub(function ($query): void {
                $query->from('stok_keluar')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('stok_keluar.id_distributor', 'distributor.id_distributor')
                    ->whereNull('stok_keluar.deleted_at');
            }, 'total_transaksi')
            ->selectSub(function ($query): void {
                $query->from('stok_keluar')
                    ->join('detail_stok_keluar', 'detail_stok_keluar.id_stok_keluar', '=', 'stok_keluar.id_stok_keluar')
                    ->selectRaw('COALESCE(SUM(detail_stok_keluar.subtotal), 0)')
                    ->whereColumn('stok_keluar.id_distributor', 'distributor.id_distributor')
                    ->whereNull('stok_keluar.deleted_at');
            }, 'total_nilai_distribusi')
            ->selectSub(function ($query): void {
                $query->from('stok_keluar')
                    ->selectRaw('MAX(tanggal_keluar)')
                    ->whereColumn('stok_keluar.id_distributor', 'distributor.id_distributor')
                    ->whereNull('stok_keluar.deleted_at');
            }, 'tanggal_transaksi_terakhir')
            ->search($filters['q'] ?? null);

        match ($filters['sort'] ?? 'nama_asc') {
            'nama_desc' => $query->orderByDesc('nama_distributor'),
            'transaksi_desc' => $query->orderByDesc('total_transaksi')->orderBy('nama_distributor'),
            'nilai_desc' => $query->orderByDesc('total_nilai_distribusi')->orderBy('nama_distributor'),
            'terbaru' => $query->orderByDesc('created_at'),
            default => $query->orderBy('nama_distributor'),
        };

        return $query;
    }

    /**
     * Ambil daftar distributor untuk halaman index.
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->listQuery($filters)->paginate($perPage)->withQueryString();
    }

    /**
     * Ringkasan statistik distributor.
     *
     * @return array<string, float|int>
     */
    public function summary(): array
    {
        $threeMonthsAgo = now('Asia/Jakarta')->subMonthsNoOverflow(3)->toDateString();
        $activeDistributorIds = DB::table('stok_keluar')
            ->whereNull('deleted_at')
            ->whereDate('tanggal_keluar', '>=', $threeMonthsAgo)
            ->distinct()
            ->pluck('id_distributor')
            ->filter();

        $totalDistributor = Distributor::count();
        $totalNilaiBulanIni = (float) DB::table('stok_keluar')
            ->join('detail_stok_keluar', 'detail_stok_keluar.id_stok_keluar', '=', 'stok_keluar.id_stok_keluar')
            ->whereNull('stok_keluar.deleted_at')
            ->whereBetween('stok_keluar.tanggal_keluar', [
                now('Asia/Jakarta')->startOfMonth()->toDateString(),
                now('Asia/Jakarta')->endOfMonth()->toDateString(),
            ])
            ->sum('detail_stok_keluar.subtotal');

        return [
            'total_distributor' => $totalDistributor,
            'distributor_aktif' => $activeDistributorIds->count(),
            'distributor_tidak_aktif' => max(0, $totalDistributor - $activeDistributorIds->count()),
            'total_nilai_bulan_ini' => $totalNilaiBulanIni,
        ];
    }

    /**
     * Generate kode distributor berikutnya.
     */
    public function generateKodeDistributor(): string
    {
        $lastNumber = (int) DB::table('distributor')
            ->selectRaw('MAX(CAST(SUBSTRING(kode_distributor, 5) AS UNSIGNED)) as nomor_terakhir')
            ->value('nomor_terakhir');

        return sprintf('DIS-%03d', $lastNumber + 1);
    }

    /**
     * Simpan distributor baru.
     *
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, Pengguna $pengguna, ?string $ipAddress): Distributor
    {
        return DB::transaction(function () use ($data, $pengguna, $ipAddress): Distributor {
            $distributor = Distributor::create([
                'kode_distributor' => $this->generateKodeDistributorForUpdate(),
                'nama_distributor' => $data['nama_distributor'],
                'alamat' => $data['alamat'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'email' => $data['email'] ?? null,
                'kontak_person' => $data['kontak_person'] ?? null,
            ]);

            $this->audit('TAMBAH_DISTRIBUTOR', $pengguna, $ipAddress, null, $distributor->only($this->auditedColumns()));

            return $distributor;
        });
    }

    /**
     * Perbarui distributor.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Distributor $distributor, array $data, Pengguna $pengguna, ?string $ipAddress): Distributor
    {
        return DB::transaction(function () use ($distributor, $data, $pengguna, $ipAddress): Distributor {
            $dataLama = $distributor->only($this->auditedColumns());

            $distributor->update([
                'nama_distributor' => $data['nama_distributor'],
                'alamat' => $data['alamat'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'email' => $data['email'] ?? null,
                'kontak_person' => $data['kontak_person'] ?? null,
            ]);

            $distributor->refresh();
            $this->audit('UBAH_DISTRIBUTOR', $pengguna, $ipAddress, $dataLama, $distributor->only($this->auditedColumns()));

            return $distributor;
        });
    }

    /**
     * Hapus distributor jika belum punya transaksi.
     */
    public function destroy(Distributor $distributor, Pengguna $pengguna, ?string $ipAddress): bool
    {
        if ($this->hasTransactions($distributor)) {
            return false;
        }

        DB::transaction(function () use ($distributor, $pengguna, $ipAddress): void {
            $dataLama = $distributor->only($this->auditedColumns());
            $distributor->delete();
            $this->audit('HAPUS_DISTRIBUTOR', $pengguna, $ipAddress, $dataLama, null);
        });

        return true;
    }

    /**
     * Cek apakah distributor punya transaksi stok keluar.
     */
    public function hasTransactions(Distributor $distributor): bool
    {
        return DB::table('stok_keluar')
            ->where('id_distributor', $distributor->id_distributor)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Statistik detail distributor.
     *
     * @return array<string, mixed>
     */
    public function distributorStats(Distributor $distributor): array
    {
        $base = DB::table('stok_keluar')
            ->leftJoin('detail_stok_keluar', 'detail_stok_keluar.id_stok_keluar', '=', 'stok_keluar.id_stok_keluar')
            ->where('stok_keluar.id_distributor', $distributor->id_distributor)
            ->whereNull('stok_keluar.deleted_at');

        $latest = DB::table('stok_keluar')
            ->where('id_distributor', $distributor->id_distributor)
            ->whereNull('deleted_at')
            ->orderByDesc('tanggal_keluar')
            ->first(['nomor_transaksi', 'tanggal_keluar']);

        $topProduct = DB::table('detail_stok_keluar')
            ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
            ->join('produk', 'produk.id_produk', '=', 'detail_stok_keluar.id_produk')
            ->where('stok_keluar.id_distributor', $distributor->id_distributor)
            ->whereNull('stok_keluar.deleted_at')
            ->selectRaw('produk.nama_produk, SUM(detail_stok_keluar.jumlah) as total_unit')
            ->groupBy('produk.id_produk', 'produk.nama_produk')
            ->orderByDesc('total_unit')
            ->first();

        return [
            'total_transaksi' => (clone $base)->distinct('stok_keluar.id_stok_keluar')->count('stok_keluar.id_stok_keluar'),
            'total_unit' => (int) (clone $base)->sum('detail_stok_keluar.jumlah'),
            'total_nilai' => (float) (clone $base)->sum('detail_stok_keluar.subtotal'),
            'transaksi_terakhir' => $latest,
            'produk_tersering' => $topProduct?->nama_produk,
        ];
    }

    /**
     * Riwayat transaksi stok keluar distributor.
     *
     * @param  array<string, mixed>  $filters
     */
    public function transactionHistory(Distributor $distributor, array $filters): LengthAwarePaginator
    {
        return DB::table('stok_keluar')
            ->leftJoin('detail_stok_keluar', 'detail_stok_keluar.id_stok_keluar', '=', 'stok_keluar.id_stok_keluar')
            ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'stok_keluar.id_pengguna')
            ->where('stok_keluar.id_distributor', $distributor->id_distributor)
            ->whereNull('stok_keluar.deleted_at')
            ->when($filters['tanggal_mulai'] ?? null, fn ($query, string $date) => $query->whereDate('stok_keluar.tanggal_keluar', '>=', $date))
            ->when($filters['tanggal_selesai'] ?? null, fn ($query, string $date) => $query->whereDate('stok_keluar.tanggal_keluar', '<=', $date))
            ->when($filters['q_transaksi'] ?? null, fn ($query, string $keyword) => $query->where('stok_keluar.nomor_transaksi', 'like', "%{$keyword}%"))
            ->selectRaw('stok_keluar.id_stok_keluar, stok_keluar.nomor_transaksi, stok_keluar.tanggal_keluar, COUNT(DISTINCT detail_stok_keluar.id_produk) as jumlah_item, COALESCE(SUM(detail_stok_keluar.jumlah), 0) as total_unit, COALESCE(SUM(detail_stok_keluar.subtotal), 0) as total_nilai, pengguna.nama_lengkap as dicatat_oleh')
            ->groupBy('stok_keluar.id_stok_keluar', 'stok_keluar.nomor_transaksi', 'stok_keluar.tanggal_keluar', 'pengguna.nama_lengkap')
            ->orderByDesc('stok_keluar.tanggal_keluar')
            ->paginate(10, ['*'], 'riwayat_page')
            ->withQueryString();
    }

    /**
     * Produk yang pernah didistribusikan ke distributor.
     */
    public function distributedProducts(Distributor $distributor): Collection
    {
        return DB::table('detail_stok_keluar')
            ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
            ->join('produk', 'produk.id_produk', '=', 'detail_stok_keluar.id_produk')
            ->leftJoin('kategori', 'kategori.id_kategori', '=', 'produk.id_kategori')
            ->where('stok_keluar.id_distributor', $distributor->id_distributor)
            ->whereNull('stok_keluar.deleted_at')
            ->selectRaw('produk.id_produk, produk.nama_produk, kategori.nama_kategori, SUM(detail_stok_keluar.jumlah) as total_unit, MAX(stok_keluar.tanggal_keluar) as terakhir_dikirim, produk.stok_terkini')
            ->groupBy('produk.id_produk', 'produk.nama_produk', 'kategori.nama_kategori', 'produk.stok_terkini')
            ->orderByDesc('total_unit')
            ->get();
    }

    /**
     * Ambil pembuat distributor dari audit trail jika tersedia.
     */
    public function creatorName(Distributor $distributor): ?string
    {
        return DB::table('audit_trail')
            ->leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'audit_trail.id_pengguna')
            ->where('audit_trail.aksi', 'TAMBAH_DISTRIBUTOR')
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(audit_trail.data_baru, "$.id_distributor")) = ?', [(string) $distributor->id_distributor])
            ->orderBy('audit_trail.waktu_aksi')
            ->value('pengguna.nama_lengkap');
    }

    /**
     * Cek duplikasi untuk AJAX.
     *
     * @return array<string, bool>
     */
    public function duplicateStatus(?string $namaDistributor, ?string $email, ?int $ignoreId = null): array
    {
        $nameQuery = DB::table('distributor');
        $emailQuery = DB::table('distributor');

        if ($ignoreId) {
            $nameQuery->where('id_distributor', '!=', $ignoreId);
            $emailQuery->where('id_distributor', '!=', $ignoreId);
        }

        return [
            'nama_exists' => $namaDistributor
                ? $nameQuery->whereRaw('LOWER(nama_distributor) = ?', [mb_strtolower($namaDistributor)])->exists()
                : false,
            'email_exists' => $email
                ? $emailQuery->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])->exists()
                : false,
        ];
    }

    /**
     * Data export distributor.
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportRows(array $filters): Collection
    {
        return $this->listQuery($filters)->get();
    }

    /**
     * Catat audit export distributor.
     *
     * @param  array<string, mixed>  $filters
     */
    public function auditExport(array $filters, int $jumlahData, Pengguna $pengguna, ?string $ipAddress): void
    {
        $this->audit('EKSPOR_DISTRIBUTOR', $pengguna, $ipAddress, null, [
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
     * Catat audit trail modul distributor.
     *
     * @param  array<string, mixed>|null  $dataLama
     * @param  array<string, mixed>|null  $dataBaru
     */
    private function audit(string $aksi, Pengguna $pengguna, ?string $ipAddress, ?array $dataLama = null, ?array $dataBaru = null): void
    {
        AuditTrail::create([
            'id_pengguna' => $pengguna->id_pengguna,
            'aksi' => $aksi,
            'modul' => 'Distributor',
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => $ipAddress,
            'waktu_aksi' => now('Asia/Jakarta'),
        ]);
    }

    /**
     * Generate kode distributor sambil mengunci tabel untuk proses simpan.
     */
    private function generateKodeDistributorForUpdate(): string
    {
        $lastCode = DB::table('distributor')
            ->lockForUpdate()
            ->orderByDesc('id_distributor')
            ->value('kode_distributor');

        $lastNumber = $lastCode ? (int) substr((string) $lastCode, 4) : 0;

        return sprintf('DIS-%03d', $lastNumber + 1);
    }
}

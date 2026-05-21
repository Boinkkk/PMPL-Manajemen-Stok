<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\StokMasuk;
use App\Services\Concerns\RecordsAuditTrail;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PDOException;

class StokMasukService
{
    use RecordsAuditTrail;

    /**
     * Buat service stok masuk baru.
     */
    public function __construct(private readonly DatabaseManager $database) {}

    /**
     * Simpan transaksi stok masuk beserta detail dan audit trail.
     *
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, Pengguna $pengguna): StokMasuk
    {
        $ipAddress = Arr::pull($data, 'ip_address');

        try {
            return $this->database->transaction(function () use ($data, $pengguna, $ipAddress): StokMasuk {
                $details = Arr::pull($data, 'details', []);

                $stokMasuk = StokMasuk::query()->create([
                    'id_stok_masuk' => $this->nextPrimaryKey(StokMasuk::class, 'id_stok_masuk'),
                    'id_supplier' => $data['id_supplier'],
                    'id_pengguna' => $pengguna->getKey(),
                    'nomor_transaksi' => $this->generateNomorTransaksi(),
                    'tanggal_masuk' => $data['tanggal_masuk'],
                    'catatan' => $data['catatan'] ?? null,
                ]);

                foreach ($details as $detail) {
                    $batch = $this->resolveBatch($detail);

                    $stokMasuk->detailStokMasuk()->create([
                        'id_produk' => $detail['id_produk'],
                        'id_batch' => $batch->getKey(),
                        'jumlah' => $detail['jumlah'],
                        'harga_beli' => $detail['harga_beli'],
                    ]);
                }

                $stokMasuk->load(['supplier', 'pengguna', 'detailStokMasuk.produk', 'detailStokMasuk.batch']);

                $this->simpanAuditTrail(
                    'create',
                    'stok_masuk',
                    null,
                    $stokMasuk->toArray(),
                    $ipAddress,
                    $pengguna
                );

                Cache::flush();

                return $stokMasuk;
            });
        } catch (QueryException|PDOException $exception) {
            throw ValidationException::withMessages([
                'details' => $this->pesanDatabase($exception),
            ]);
        }
    }

    /**
     * Buat nomor transaksi unik dengan format SM-YYYYMMDD-XXXX.
     */
    public function generateNomorTransaksi(): string
    {
        $prefix = 'SM-'.now()->format('Ymd');
        $lastNumber = StokMasuk::withTrashed()
            ->where('nomor_transaksi', 'like', "{$prefix}-%")
            ->orderByDesc('nomor_transaksi')
            ->value('nomor_transaksi');

        $nextNumber = $lastNumber === null ? 1 : ((int) Str::afterLast($lastNumber, '-')) + 1;

        return sprintf('%s-%04d', $prefix, $nextNumber);
    }

    /**
     * Periksa stok produk, tersedia untuk kebutuhan reuse service.
     */
    public function cekKetersediaanStok(int $idProduk, int $jumlah): bool
    {
        $produk = Produk::query()->find($idProduk);

        return $produk !== null && $produk->stok_terkini >= $jumlah;
    }

    /**
     * Arsipkan transaksi stok masuk tanpa membalik stok yang sudah diproses trigger.
     */
    public function destroy(StokMasuk $stokMasuk, Pengguna $pengguna, ?string $ipAddress): void
    {
        $this->database->transaction(function () use ($stokMasuk, $pengguna, $ipAddress): void {
            $stokMasuk->load(['supplier', 'pengguna', 'detailStokMasuk.produk', 'detailStokMasuk.batch']);

            $this->simpanAuditTrail(
                'delete',
                'stok_masuk',
                $stokMasuk->toArray(),
                null,
                $ipAddress,
                $pengguna
            );

            $stokMasuk->delete();
        });
    }

    /**
     * Ambil atau buat batch produk dari detail stok masuk.
     *
     * @param  array<string, mixed>  $detail
     */
    private function resolveBatch(array $detail): Batch
    {
        $batch = Batch::query()
            ->where('id_produk', $detail['id_produk'])
            ->where('nomor_batch', $detail['nomor_batch'])
            ->first();

        if ($batch !== null) {
            return $batch;
        }

        return Batch::query()->create([
            'id_batch' => $this->nextPrimaryKey(Batch::class, 'id_batch'),
            'id_produk' => $detail['id_produk'],
            'nomor_batch' => $detail['nomor_batch'],
            'tanggal_produksi' => $detail['tanggal_produksi'] ?? null,
            'tanggal_expired' => $detail['tanggal_expired'],
            'keterangan' => $detail['keterangan'] ?? null,
        ]);
    }

    /**
     * Buat primary key manual untuk tabel yang tidak auto increment.
     *
     * @param  class-string<Model>  $modelClass
     */
    private function nextPrimaryKey(string $modelClass, string $primaryKey): int
    {
        return ((int) $modelClass::query()->lockForUpdate()->max($primaryKey)) + 1;
    }

    /**
     * Ubah error database menjadi pesan validasi yang ramah.
     */
    private function pesanDatabase(QueryException|PDOException $exception): string
    {
        $message = $exception->getMessage();

        if (str_contains($message, 'Duplicate entry')) {
            return 'Nomor transaksi atau nomor batch sudah digunakan. Silakan coba lagi.';
        }

        return 'Transaksi stok masuk gagal disimpan. Periksa kembali data yang dimasukkan.';
    }
}

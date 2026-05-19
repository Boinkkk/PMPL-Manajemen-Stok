<?php

namespace App\Services;

use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\StokKeluar;
use App\Services\Concerns\RecordsAuditTrail;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PDOException;

class StokKeluarService
{
    use RecordsAuditTrail;

    /**
     * Buat service stok keluar baru.
     */
    public function __construct(private readonly DatabaseManager $database) {}

    /**
     * Simpan transaksi stok keluar beserta detail dan audit trail.
     *
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, Pengguna $pengguna): StokKeluar
    {
        $ipAddress = Arr::pull($data, 'ip_address');

        try {
            return $this->database->transaction(function () use ($data, $pengguna, $ipAddress): StokKeluar {
                $details = Arr::pull($data, 'details', []);

                foreach ($details as $detail) {
                    if (! $this->cekKetersediaanStok((int) $detail['id_produk'], (int) $detail['jumlah'])) {
                        throw ValidationException::withMessages([
                            'details' => 'Stok produk tidak mencukupi untuk transaksi ini.',
                        ]);
                    }
                }

                $stokKeluar = StokKeluar::query()->create([
                    'id_stok_keluar' => $this->nextPrimaryKey(StokKeluar::class, 'id_stok_keluar'),
                    'id_pengguna' => $pengguna->getKey(),
                    'id_distributor' => $data['id_distributor'],
                    'id_order' => $data['id_order'] ?? null,
                    'nomor_transaksi' => $this->generateNomorTransaksi(),
                    'tanggal_keluar' => $data['tanggal_keluar'],
                    'catatan' => $data['catatan'] ?? null,
                ]);

                foreach ($details as $detail) {
                    $stokKeluar->detailStokKeluar()->create([
                        'id_produk' => $detail['id_produk'],
                        'id_batch' => $detail['id_batch'],
                        'jumlah' => $detail['jumlah'],
                        'harga_jual' => $detail['harga_jual'],
                    ]);
                }

                $stokKeluar->load(['distributor', 'pengguna', 'orderDistribusi', 'detailStokKeluar.produk', 'detailStokKeluar.batch']);

                $this->simpanAuditTrail(
                    'create',
                    'stok_keluar',
                    null,
                    $stokKeluar->toArray(),
                    $ipAddress,
                    $pengguna
                );

                return $stokKeluar;
            });
        } catch (QueryException|PDOException $exception) {
            throw ValidationException::withMessages([
                'details' => $this->pesanDatabase($exception),
            ]);
        }
    }

    /**
     * Buat nomor transaksi unik dengan format SK-YYYYMMDD-XXXX.
     */
    public function generateNomorTransaksi(): string
    {
        $prefix = 'SK-'.now()->format('Ymd');
        $lastNumber = StokKeluar::withTrashed()
            ->where('nomor_transaksi', 'like', "{$prefix}-%")
            ->orderByDesc('nomor_transaksi')
            ->value('nomor_transaksi');

        $nextNumber = $lastNumber === null ? 1 : ((int) Str::afterLast($lastNumber, '-')) + 1;

        return sprintf('%s-%04d', $prefix, $nextNumber);
    }

    /**
     * Periksa apakah stok produk cukup untuk jumlah keluar.
     */
    public function cekKetersediaanStok(int $idProduk, int $jumlah): bool
    {
        $produk = Produk::query()->find($idProduk);

        return $produk !== null && $produk->stok_terkini >= $jumlah;
    }

    /**
     * Arsipkan transaksi stok keluar tanpa membalik stok yang sudah diproses trigger.
     */
    public function destroy(StokKeluar $stokKeluar, Pengguna $pengguna, ?string $ipAddress): void
    {
        $this->database->transaction(function () use ($stokKeluar, $pengguna, $ipAddress): void {
            $stokKeluar->load(['distributor', 'pengguna', 'orderDistribusi', 'detailStokKeluar.produk', 'detailStokKeluar.batch']);

            $this->simpanAuditTrail(
                'delete',
                'stok_keluar',
                $stokKeluar->toArray(),
                null,
                $ipAddress,
                $pengguna
            );

            $stokKeluar->delete();
        });
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

        if (str_contains($message, 'Stok tidak mencukupi')) {
            return 'Stok tidak mencukupi untuk transaksi ini. Data terbaru mungkin berubah, silakan muat ulang halaman.';
        }

        if (str_contains($message, 'Duplicate entry')) {
            return 'Nomor transaksi sudah digunakan. Silakan coba lagi.';
        }

        return 'Transaksi stok keluar gagal disimpan. Periksa kembali data yang dimasukkan.';
    }
}

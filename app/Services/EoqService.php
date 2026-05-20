<?php

namespace App\Services;

use App\Models\DataEoq;
use App\Models\DetailStokKeluar;
use App\Models\Pengguna;
use App\Models\Produk;
use App\Services\Concerns\AuditTrailTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EoqService
{
    use AuditTrailTrait;

    /**
     * Ambil data EOQ untuk halaman monitoring.
     */
    public function paginate(?string $keyword = null, int $perPage = 15): LengthAwarePaginator
    {
        return DataEoq::query()
            ->with(['produk.kategori', 'produk.satuan'])
            ->when($keyword, function (Builder $query, string $keyword): void {
                $query->whereHas('produk', function (Builder $query) use ($keyword): void {
                    $query
                        ->where('nama_produk', 'like', "%{$keyword}%")
                        ->orWhere('kode_produk', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('id_eoq')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Ambil ringkasan data EOQ.
     *
     * @return array<string, int>
     */
    public function summary(): array
    {
        $produkTable = (new Produk)->getTable();
        $eoqTable = (new DataEoq)->getTable();

        return [
            'total_data' => DataEoq::query()->count(),
            'rata_rata_eoq' => (int) round((float) DataEoq::query()->avg('eoq')),
            'tanpa_permintaan' => DataEoq::query()->where('permintaan_tahunan', 0)->count(),
            'perlu_reorder' => DataEoq::query()
                ->join($produkTable, "{$produkTable}.id_produk", '=', "{$eoqTable}.id_produk")
                ->where("{$eoqTable}.eoq", '>', 0)
                ->whereColumn("{$produkTable}.stok_terkini", '<=', "{$eoqTable}.eoq")
                ->count(),
        ];
    }

    /**
     * Sinkronkan data EOQ dari master produk dan transaksi stok keluar.
     *
     * @return array<string, int>
     */
    public function sync(Pengguna $pengguna, ?string $ipAddress): array
    {
        $demandByProduct = $this->annualDemandByProduct();
        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($demandByProduct, $ipAddress, $pengguna, &$created, &$updated): void {
            Produk::query()
                ->orderBy('id_produk')
                ->chunk(100, function ($products) use ($demandByProduct, &$created, &$updated): void {
                    foreach ($products as $product) {
                        $dataEoq = DataEoq::query()
                            ->where('id_produk', $product->id_produk)
                            ->lockForUpdate()
                            ->first();

                        $values = $this->syncValues($product, $dataEoq, (int) ($demandByProduct[$product->id_produk] ?? 0));

                        if ($dataEoq instanceof DataEoq) {
                            $dataEoq->update($values);
                            $updated++;

                            continue;
                        }

                        DataEoq::query()->create($values);
                        $created++;
                    }
                });

            $this->simpanAuditTrail('SINKRONISASI_EOQ', 'Monitoring EOQ', null, [
                'dibuat' => $created,
                'diperbarui' => $updated,
            ], $ipAddress, $pengguna);
        });

        return [
            'dibuat' => $created,
            'diperbarui' => $updated,
        ];
    }

    /**
     * Perbarui satu data EOQ dan hitung ulang nilainya.
     *
     * @param  array<string, int>  $data
     */
    public function update(DataEoq $dataEoq, array $data, Pengguna $pengguna, ?string $ipAddress): DataEoq
    {
        return DB::transaction(function () use ($dataEoq, $data, $pengguna, $ipAddress): DataEoq {
            $oldData = $dataEoq->only([
                'id_eoq',
                'id_produk',
                'permintaan_tahunan',
                'biaya_pemesanan',
                'biaya_penyimpanan',
                'eoq',
            ]);

            $data['eoq'] = $this->calculateEoq(
                (int) $data['permintaan_tahunan'],
                (int) $data['biaya_pemesanan'],
                (int) $data['biaya_penyimpanan'],
            );

            $dataEoq->update($data);

            $this->simpanAuditTrail('UBAH_DATA_EOQ', 'Monitoring EOQ', $oldData, $dataEoq->fresh()?->toArray(), $ipAddress, $pengguna);

            return $dataEoq->fresh(['produk']) ?? $dataEoq;
        });
    }

    /**
     * Hitung nilai EOQ.
     */
    public function calculateEoq(int $annualDemand, int $orderCost, int $holdingCost): int
    {
        if ($annualDemand <= 0 || $orderCost <= 0 || $holdingCost <= 0) {
            return 0;
        }

        return (int) ceil(sqrt((2 * $annualDemand * $orderCost) / $holdingCost));
    }

    /**
     * Ambil permintaan produk dari stok keluar 365 hari terakhir.
     *
     * @return array<int, int>
     */
    private function annualDemandByProduct(): array
    {
        return DetailStokKeluar::query()
            ->join('stok_keluar', 'stok_keluar.id_stok_keluar', '=', 'detail_stok_keluar.id_stok_keluar')
            ->whereNull('stok_keluar.deleted_at')
            ->whereBetween('stok_keluar.tanggal_keluar', [
                today('Asia/Jakarta')->subDays(365)->toDateString(),
                today('Asia/Jakarta')->toDateString(),
            ])
            ->selectRaw('detail_stok_keluar.id_produk, COALESCE(SUM(detail_stok_keluar.jumlah), 0) as total')
            ->groupBy('detail_stok_keluar.id_produk')
            ->pluck('total', 'detail_stok_keluar.id_produk')
            ->map(fn(mixed $total): int => (int) $total)
            ->all();
    }

    /**
     * Susun nilai hasil sinkronisasi untuk satu produk.
     *
     * @return array<string, int>
     */
    private function syncValues(Produk $product, ?DataEoq $dataEoq, int $annualDemand): array
    {
        $orderCost = (int) ($dataEoq?->biaya_pemesanan ?: max(1, round((float) $product->harga_satuan)));
        $holdingCost = (int) ($dataEoq?->biaya_penyimpanan ?: max(1, ceil((float) $product->harga_satuan * 0.1)));

        return [
            'id_produk' => $product->id_produk,
            'permintaan_tahunan' => $annualDemand,
            'biaya_pemesanan' => $orderCost,
            'biaya_penyimpanan' => $holdingCost,
            'eoq' => $this->calculateEoq($annualDemand, $orderCost, $holdingCost),
        ];
    }

    /**
     * Update stok_minimum produk berdasarkan nilai EOQ.
     */
    /**
     * Update stok_minimum semua produk berdasarkan nilai EOQ.
     *
     * @return array<string, int>
     */
    public function updateStokMinimumByEoq(Pengguna $pengguna, ?string $ipAddress): array
    {
        $updated = 0;

        DB::transaction(function () use (&$updated, $pengguna, $ipAddress): void {
            DataEoq::query()
                ->where('eoq', '>', 0)
                ->orderBy('id_eoq')
                ->chunk(100, function ($dataEoqs) use (&$updated): void {
                    foreach ($dataEoqs as $dataEoq) {
                        Produk::query()
                            ->where('id_produk', $dataEoq->id_produk)
                            ->update([
                                'stok_minimum' => $dataEoq->eoq,
                            ]);

                        $updated++;
                    }
                });

            $this->simpanAuditTrail(
                'UPDATE_STOK_MINIMUM_DARI_EOQ',
                'Monitoring EOQ',
                null,
                ['jumlah_produk_diperbarui' => $updated],
                $ipAddress,
                $pengguna
            );
        });

        return [
            'diperbarui' => $updated,
        ];
    }
}

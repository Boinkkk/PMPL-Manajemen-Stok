<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\DetailOrder;
use App\Models\OrderDistribusi;
use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\StokKeluar;
use App\Services\Concerns\AuditTrailTrait;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PDOException;

class OrderDistribusiService
{
    use AuditTrailTrait;

    /**
     * Buat service order distribusi baru.
     */
    public function __construct(private readonly DatabaseManager $database) {}

    /**
     * Simpan order distribusi baru.
     *
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, Pengguna $pengguna): OrderDistribusi
    {
        $ipAddress = Arr::pull($data, 'ip_address');

        try {
            return $this->database->transaction(function () use ($data, $pengguna, $ipAddress): OrderDistribusi {
                $details = Arr::pull($data, 'details', []);

                $order = OrderDistribusi::query()->create([
                    'id_order' => $this->nextPrimaryKey(OrderDistribusi::class, 'id_order'),
                    'id_distributor' => $data['id_distributor'],
                    'id_pengguna' => $pengguna->getKey(),
                    'nomor_order' => $this->generateNomorOrder($data['tanggal_order']),
                    'tanggal_order' => $data['tanggal_order'],
                    'status' => 'pending',
                    'catatan' => $data['catatan'] ?? null,
                ]);

                $this->syncDetails($order, $details);

                $order->load(['distributor', 'pengguna', 'detailOrders.produk.satuan']);

                $this->simpanAuditTrail('BUAT_ORDER', 'Order Distribusi', null, $order->toArray(), $ipAddress, $pengguna);

                Cache::flush();

                return $order;
            }, attempts: 5);
        } catch (QueryException|PDOException $exception) {
            throw ValidationException::withMessages([
                'nomor_order' => $this->pesanDatabase($exception),
            ]);
        }
    }

    /**
     * Perbarui order distribusi yang masih pending.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(OrderDistribusi $order, array $data, Pengguna $pengguna): OrderDistribusi
    {
        $ipAddress = Arr::pull($data, 'ip_address');

        return $this->database->transaction(function () use ($order, $data, $pengguna, $ipAddress): OrderDistribusi {
            $order = OrderDistribusi::query()
                ->with(['detailOrders.produk'])
                ->lockForUpdate()
                ->findOrFail($order->getKey());

            $this->pastikanPending($order, 'Order tidak dapat diubah karena sudah diproses.');

            $dataLama = $order->toArray();
            $details = Arr::pull($data, 'details', []);

            $order->update([
                'id_distributor' => $data['id_distributor'],
                'tanggal_order' => $data['tanggal_order'],
                'catatan' => $data['catatan'] ?? null,
            ]);

            $order->detailOrders()->delete();
            $this->syncDetails($order, $details);

            $order->load(['distributor', 'pengguna', 'detailOrders.produk.satuan']);

            $this->simpanAuditTrail('EDIT_ORDER', 'Order Distribusi', $dataLama, $order->toArray(), $ipAddress, $pengguna);

            Cache::flush();

            return $order;
        }, attempts: 5);
    }

    /**
     * Setujui order, buat stok keluar, dan alokasikan batch FIFO.
     *
     * @param  array<string, mixed>  $data
     */
    public function approve(OrderDistribusi $order, array $data, Pengguna $pengguna): OrderDistribusi
    {
        $ipAddress = Arr::pull($data, 'ip_address');

        try {
            return $this->database->transaction(function () use ($order, $data, $pengguna, $ipAddress): OrderDistribusi {
                $order = OrderDistribusi::query()
                    ->with(['detailOrders.produk', 'distributor'])
                    ->lockForUpdate()
                    ->findOrFail($order->getKey());

                $this->pastikanPending($order, 'Order tidak dapat disetujui karena sudah diproses.');

                $requestedApproval = collect($data['details'])
                    ->keyBy(fn (array $detail): int => (int) $detail['id_detail_order']);

                $produkIds = $order->detailOrders->pluck('id_produk')->filter()->unique()->values();
                $products = Produk::query()
                    ->whereIn('id_produk', $produkIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_produk');

                $order->update([
                    'status' => 'disetujui',
                    'tanggal_diproses' => now()->toDateString(),
                ]);

                $stokKeluar = StokKeluar::query()->create([
                    'id_stok_keluar' => $this->nextPrimaryKey(StokKeluar::class, 'id_stok_keluar'),
                    'id_pengguna' => $pengguna->getKey(),
                    'id_distributor' => $order->id_distributor,
                    'id_order' => $order->getKey(),
                    'nomor_transaksi' => $this->generateNomorStokKeluar(),
                    'tanggal_keluar' => now()->toDateString(),
                    'catatan' => $data['catatan_persetujuan'] ?? 'Stok keluar otomatis dari order '.$order->nomor_order,
                ]);

                foreach ($order->detailOrders as $detailOrder) {
                    $approval = $requestedApproval->get($detailOrder->getKey());
                    $jumlahDisetujui = (int) ($approval['jumlah_disetujui'] ?? 0);
                    $produk = $products->get($detailOrder->id_produk);

                    if ($jumlahDisetujui > $detailOrder->jumlah_diminta) {
                        throw ValidationException::withMessages([
                            'details' => "Jumlah disetujui untuk {$detailOrder->produk?->nama_produk} melebihi jumlah diminta.",
                        ]);
                    }

                    if ($produk === null || $jumlahDisetujui > $produk->stok_terkini) {
                        throw ValidationException::withMessages([
                            'details' => "Stok {$detailOrder->produk?->nama_produk} tidak mencukupi. Tersedia: ".($produk?->stok_terkini ?? 0).'.',
                        ]);
                    }

                    $detailOrder->update(['jumlah_disetujui' => $jumlahDisetujui]);

                    if ($jumlahDisetujui > 0) {
                        $this->buatDetailStokKeluarFifo($stokKeluar, $detailOrder, $jumlahDisetujui);
                    }
                }

                $order->update([
                    'status' => 'selesai',
                    'tanggal_diproses' => now()->toDateString(),
                    'catatan' => $this->appendCatatan($order->catatan, '[DISETUJUI]: '.($data['catatan_persetujuan'] ?? 'Order disetujui dan stok keluar dibuat otomatis.')),
                ]);

                $order->load(['distributor', 'pengguna', 'detailOrders.produk.satuan', 'stokKeluar.detailStokKeluar.produk', 'stokKeluar.detailStokKeluar.batch']);

                $this->simpanAuditTrail('SETUJUI_ORDER', 'Order Distribusi', null, [
                    'order' => $order->toArray(),
                    'stok_keluar' => $stokKeluar->load('detailStokKeluar')->toArray(),
                ], $ipAddress, $pengguna);

                Cache::flush();

                return $order;
            }, attempts: 5);
        } catch (QueryException|PDOException $exception) {
            throw ValidationException::withMessages([
                'details' => $this->pesanDatabase($exception),
            ]);
        }
    }

    /**
     * Tolak order pending dengan alasan.
     */
    public function reject(OrderDistribusi $order, string $alasan, Pengguna $pengguna, ?string $ipAddress): OrderDistribusi
    {
        return $this->database->transaction(function () use ($order, $alasan, $pengguna, $ipAddress): OrderDistribusi {
            $order = OrderDistribusi::query()
                ->with(['detailOrders.produk'])
                ->lockForUpdate()
                ->findOrFail($order->getKey());

            $this->pastikanPending($order, 'Order tidak dapat ditolak karena sudah diproses.');

            $dataLama = $order->toArray();

            $order->update([
                'status' => 'ditolak',
                'tanggal_diproses' => now()->toDateString(),
                'catatan' => $this->appendCatatan($order->catatan, '[DITOLAK]: '.$alasan),
            ]);

            $order->load(['distributor', 'pengguna', 'detailOrders.produk']);

            $this->simpanAuditTrail('TOLAK_ORDER', 'Order Distribusi', $dataLama, $order->toArray(), $ipAddress, $pengguna);

            Cache::flush();

            return $order;
        });
    }

    /**
     * Batalkan order yang masih pending.
     */
    public function cancel(OrderDistribusi $order, Pengguna $pengguna, ?string $ipAddress): OrderDistribusi
    {
        return $this->database->transaction(function () use ($order, $pengguna, $ipAddress): OrderDistribusi {
            $order = OrderDistribusi::query()
                ->with(['detailOrders.produk'])
                ->lockForUpdate()
                ->findOrFail($order->getKey());

            $this->pastikanPending($order, 'Order tidak dapat dibatalkan karena sudah diproses.');

            $dataLama = $order->toArray();

            $order->update([
                'status' => 'ditolak',
                'tanggal_diproses' => now()->toDateString(),
                'catatan' => $this->appendCatatan($order->catatan, '[DIBATALKAN OLEH PENGGUNA]: dibatalkan sebelum diproses'),
            ]);

            $order->load(['distributor', 'pengguna', 'detailOrders.produk']);

            $this->simpanAuditTrail('BATALKAN_ORDER', 'Order Distribusi', $dataLama, [
                'order' => $order->toArray(),
                'waktu_pembatalan' => now()->toDateTimeString(),
            ], $ipAddress, $pengguna);

            Cache::flush();

            return $order;
        });
    }

    /**
     * Buat ulang order yang ditolak sebagai order pending baru.
     */
    public function reorder(OrderDistribusi $order, Pengguna $pengguna, ?string $ipAddress): OrderDistribusi
    {
        return $this->database->transaction(function () use ($order, $pengguna, $ipAddress): OrderDistribusi {
            $order->load(['detailOrders']);

            if ($order->status !== 'ditolak') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya order yang ditolak yang dapat dibuat ulang.',
                ]);
            }

            $newOrder = OrderDistribusi::query()->create([
                'id_order' => $this->nextPrimaryKey(OrderDistribusi::class, 'id_order'),
                'id_distributor' => $order->id_distributor,
                'id_pengguna' => $pengguna->getKey(),
                'nomor_order' => $this->generateNomorOrder(now()->toDateString()),
                'tanggal_order' => now()->toDateString(),
                'status' => 'pending',
                'catatan' => 'Dibuat ulang dari order '.$order->nomor_order,
            ]);

            foreach ($order->detailOrders as $detail) {
                $newOrder->detailOrders()->create([
                    'id_produk' => $detail->id_produk,
                    'jumlah_diminta' => $detail->jumlah_diminta,
                    'jumlah_disetujui' => 0,
                    'harga_satuan' => $detail->harga_satuan,
                    'catatan' => $detail->catatan,
                ]);
            }

            $newOrder->load(['distributor', 'pengguna', 'detailOrders.produk']);

            $this->simpanAuditTrail('BUAT_ORDER', 'Order Distribusi', null, [
                'order' => $newOrder->toArray(),
                'sumber_order' => $order->nomor_order,
            ], $ipAddress, $pengguna);

            Cache::flush();

            return $newOrder;
        }, attempts: 5);
    }

    /**
     * Buat nomor order unik dengan format ORD-YYYYMMDD-XXXX.
     */
    public function generateNomorOrder(?string $tanggalOrder = null): string
    {
        $tanggal = filled($tanggalOrder) ? date('Ymd', strtotime((string) $tanggalOrder)) : now()->format('Ymd');
        $prefix = "ORD-{$tanggal}";
        $lastNumber = OrderDistribusi::query()
            ->where('nomor_order', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->orderByDesc('nomor_order')
            ->value('nomor_order');

        $nextNumber = $lastNumber === null ? 1 : ((int) Str::afterLast($lastNumber, '-')) + 1;

        return sprintf('%s-%04d', $prefix, $nextNumber);
    }

    /**
     * Catat audit trail cetak surat jalan.
     */
    public function recordPrint(OrderDistribusi $order, Pengguna $pengguna, ?string $ipAddress): void
    {
        $this->simpanAuditTrail('CETAK_SURAT_JALAN', 'Order Distribusi', null, [
            'nomor_order' => $order->nomor_order,
            'waktu_cetak' => now()->toDateTimeString(),
        ], $ipAddress, $pengguna);
    }

    /**
     * Catat audit trail ekspor order.
     *
     * @param  array<string, mixed>  $filters
     */
    public function recordExport(array $filters, int $jumlahData, Pengguna $pengguna, ?string $ipAddress): void
    {
        $this->simpanAuditTrail('EKSPOR_ORDER', 'Order Distribusi', null, [
            'filter' => $filters,
            'jumlah_data' => $jumlahData,
            'waktu_ekspor' => now()->toDateTimeString(),
        ], $ipAddress, $pengguna);
    }

    /**
     * Sinkronkan detail order dari request.
     *
     * @param  array<int, array<string, mixed>>  $details
     */
    private function syncDetails(OrderDistribusi $order, array $details): void
    {
        foreach ($details as $detail) {
            $order->detailOrders()->create([
                'id_produk' => $detail['id_produk'],
                'jumlah_diminta' => $detail['jumlah_diminta'],
                'jumlah_disetujui' => 0,
                'harga_satuan' => $detail['harga_satuan'],
                'catatan' => $detail['catatan'] ?? null,
            ]);
        }
    }

    /**
     * Buat detail stok keluar berdasarkan FIFO batch belum kedaluwarsa.
     */
    private function buatDetailStokKeluarFifo(StokKeluar $stokKeluar, DetailOrder $detailOrder, int $jumlahDisetujui): void
    {
        $sisa = $jumlahDisetujui;
        $batches = $this->batchTersedia((int) $detailOrder->id_produk);

        foreach ($batches as $batch) {
            if ($sisa <= 0) {
                break;
            }

            $ambil = min($sisa, (int) $batch->stok_batch);

            if ($ambil <= 0) {
                continue;
            }

            $stokKeluar->detailStokKeluar()->create([
                'id_produk' => $detailOrder->id_produk,
                'id_batch' => $batch->id_batch,
                'jumlah' => $ambil,
                'harga_jual' => $detailOrder->harga_satuan,
            ]);

            $sisa -= $ambil;
        }

        if ($sisa > 0) {
            throw ValidationException::withMessages([
                'details' => "Batch aktif untuk {$detailOrder->produk?->nama_produk} tidak mencukupi.",
            ]);
        }
    }

    /**
     * Ambil daftar batch aktif beserta stok hasil kalkulasi riwayat.
     *
     * @return Collection<int, Batch>
     */
    private function batchTersedia(int $idProduk): Collection
    {
        return Batch::query()
            ->select('batch.*')
            ->selectSub(
                'COALESCE((SELECT SUM(dsm.jumlah) FROM detail_stok_masuk dsm WHERE dsm.id_batch = batch.id_batch), 0)
                - COALESCE((SELECT SUM(dsk.jumlah) FROM detail_stok_keluar dsk WHERE dsk.id_batch = batch.id_batch), 0)',
                'stok_batch'
            )
            ->where('id_produk', $idProduk)
            ->whereDate('tanggal_expired', '>=', today())
            ->orderBy('tanggal_expired')
            ->orderBy('id_batch')
            ->lockForUpdate()
            ->get()
            ->filter(fn (Batch $batch): bool => (int) $batch->stok_batch > 0)
            ->values();
    }

    /**
     * Pastikan order masih pending.
     */
    private function pastikanPending(OrderDistribusi $order, string $message): void
    {
        if (! $order->isPending()) {
            throw ValidationException::withMessages([
                'status' => $message,
            ]);
        }
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
     * Buat nomor transaksi stok keluar.
     */
    private function generateNomorStokKeluar(): string
    {
        $prefix = 'SK-'.now()->format('Ymd');
        $lastNumber = StokKeluar::withTrashed()
            ->where('nomor_transaksi', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->orderByDesc('nomor_transaksi')
            ->value('nomor_transaksi');

        $nextNumber = $lastNumber === null ? 1 : ((int) Str::afterLast($lastNumber, '-')) + 1;

        return sprintf('%s-%04d', $prefix, $nextNumber);
    }

    /**
     * Tambahkan catatan baru ke catatan lama.
     */
    private function appendCatatan(?string $catatanLama, string $catatanBaru): string
    {
        return trim(collect([$catatanLama, $catatanBaru])->filter()->implode(PHP_EOL));
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
            return 'Nomor order atau nomor transaksi sudah digunakan. Silakan coba lagi.';
        }

        return 'Order distribusi gagal diproses. Periksa kembali data yang dimasukkan.';
    }
}

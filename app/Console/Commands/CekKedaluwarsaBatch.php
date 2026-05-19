<?php

namespace App\Console\Commands;

use App\Models\Notifikasi;
use App\Models\Pengguna;
use App\Services\MonitoringService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('monitoring:cek-kedaluwarsa-batch')]
#[Description('Cek batch yang mendekati atau sudah melewati tanggal kedaluwarsa.')]
class CekKedaluwarsaBatch extends Command
{
    /**
     * Jalankan command monitoring kedaluwarsa batch.
     */
    public function handle(MonitoringService $monitoringService): int
    {
        $startedAt = now('Asia/Jakarta');
        $checked = 0;
        $created = 0;

        try {
            $users = Pengguna::query()
                ->where('status', 'aktif')
                ->get(['id_pengguna']);

            $nearExpiryBatches = $monitoringService->batchWithStockQuery()
                ->with('produk')
                ->whereBetween('tanggal_expired', [today('Asia/Jakarta')->toDateString(), today('Asia/Jakarta')->addDays(30)->toDateString()])
                ->having('stok_batch', '>', 0)
                ->get();

            foreach ($nearExpiryBatches as $batch) {
                $checked++;
                $sisaHari = today('Asia/Jakarta')->diffInDays($batch->tanggal_expired, false);
                $message = sprintf(
                    'Produk %s (Batch %s) akan kedaluwarsa pada %s (sisa %d hari)',
                    $batch->produk?->nama_produk ?? '-',
                    $batch->nomor_batch,
                    $batch->tanggal_expired?->locale('id')->translatedFormat('d F Y'),
                    $sisaHari
                );

                foreach ($users as $user) {
                    if ($this->notificationExistsToday((int) $user->id_pengguna, (int) $batch->id_produk, $message)) {
                        continue;
                    }

                    Notifikasi::query()->create([
                        'id_produk' => $batch->id_produk,
                        'id_pengguna' => $user->id_pengguna,
                        'jenis' => 'kedaluwarsa',
                        'pesan' => $message,
                    ]);

                    $created++;
                }
            }

            $expiredBatches = $monitoringService->batchWithStockQuery()
                ->with('produk')
                ->whereDate('tanggal_expired', '<', today('Asia/Jakarta')->toDateString())
                ->having('stok_batch', '>', 0)
                ->get();

            foreach ($expiredBatches as $batch) {
                $checked++;
                $message = sprintf(
                    'PERHATIAN: Produk %s (Batch %s) telah melewati tanggal kedaluwarsa. Segera lakukan pengecekan fisik.',
                    $batch->produk?->nama_produk ?? '-',
                    $batch->nomor_batch
                );

                foreach ($users as $user) {
                    if ($this->notificationExistsEver((int) $user->id_pengguna, (int) $batch->id_produk, $message)) {
                        continue;
                    }

                    Notifikasi::query()->create([
                        'id_produk' => $batch->id_produk,
                        'id_pengguna' => $user->id_pengguna,
                        'jenis' => 'kedaluwarsa',
                        'pesan' => $message,
                    ]);

                    $created++;
                }
            }

            Log::info('Command monitoring kedaluwarsa batch selesai.', [
                'batch_dicek' => $checked,
                'notifikasi_dibuat' => $created,
                'waktu_eksekusi' => $startedAt->toDateTimeString(),
                'durasi_detik' => $startedAt->diffInSeconds(now('Asia/Jakarta')),
            ]);

            $this->info("Selesai. Batch dicek: {$checked}. Notifikasi dibuat: {$created}.");

            return self::SUCCESS;
        } catch (Throwable $throwable) {
            Log::error('Command monitoring kedaluwarsa batch gagal.', [
                'batch_dicek' => $checked,
                'notifikasi_dibuat' => $created,
                'waktu_eksekusi' => $startedAt->toDateTimeString(),
                'error' => $throwable->getMessage(),
            ]);

            $this->error('Command monitoring kedaluwarsa batch gagal: '.$throwable->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Cek notifikasi kedaluwarsa sudah dibuat hari ini.
     */
    private function notificationExistsToday(int $idPengguna, int $idProduk, string $message): bool
    {
        return Notifikasi::query()
            ->where('id_pengguna', $idPengguna)
            ->where('id_produk', $idProduk)
            ->where('jenis', 'kedaluwarsa')
            ->where('pesan', $message)
            ->whereDate('created_at', today('Asia/Jakarta'))
            ->exists();
    }

    /**
     * Cek notifikasi kedaluwarsa sudah pernah dibuat.
     */
    private function notificationExistsEver(int $idPengguna, int $idProduk, string $message): bool
    {
        return Notifikasi::query()
            ->where('id_pengguna', $idPengguna)
            ->where('id_produk', $idProduk)
            ->where('jenis', 'kedaluwarsa')
            ->where('pesan', $message)
            ->exists();
    }
}

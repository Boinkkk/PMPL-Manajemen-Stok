<?php

namespace App\Services;

use App\Models\ReturProduk;
use App\Models\Supplier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    public function kirimNotifikasiSupplier(ReturProduk $retur, string $tipe): bool
    {
        $supplier = $retur->supplier ?? Supplier::query()->whereNotNull('telepon')->first();

        if (! $supplier || ! $supplier->telepon) {
            Log::warning('WhatsApp notifikasi tidak dikirim karena supplier tidak ditemukan atau tanpa nomor.', [
                'retur_id' => $retur->id_retur,
            ]);

            return false;
        }

        $message = $this->buildMessage($retur, $tipe);
        $payload = [
            'phone' => $supplier->telepon,
            'message' => $message,
        ];

        $apiUrl = env('FONNTE_API_URL', 'https://api.fonnte.id/wa/send');
        $apiToken = env('FONNTE_API_TOKEN');

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiToken ? "Bearer {$apiToken}" : '',
                'Accept' => 'application/json',
            ])->post($apiUrl, $payload);

            if ($response->successful()) {
                Log::info('WhatsApp notifikasi supplier terkirim.', [
                    'retur_id' => $retur->id_retur,
                    'supplier_id' => $supplier->id_supplier,
                    'tipe' => $tipe,
                ]);

                return true;
            }

            Log::warning('WhatsApp notifikasi supplier gagal.', [
                'retur_id' => $retur->id_retur,
                'supplier_id' => $supplier->id_supplier,
                'tipe' => $tipe,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $exception) {
            Log::error('Kesalahan saat mengirim notifikasi WhatsApp supplier.', [
                'retur_id' => $retur->id_retur,
                'tipe' => $tipe,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function buildMessage(ReturProduk $retur, string $tipe): string
    {
        $produk = $retur->produk?->nama_produk ?: 'Produk tidak dikenal';
        $distributor = $retur->distributor?->nama_distributor ?: 'Distributor tidak dikenal';

        return match ($tipe) {
            'retur_baru' => "Notifikasi retur baru:\nDistributor: {$distributor}\nProduk: {$produk}\nJumlah: {$retur->jumlah_retur}\nAlasan: {$retur->alasan}.",
            'retur_diproses' => $this->buildDiprosesMessage($retur, $produk, $distributor),
            default => "Notifikasi retur untuk {$produk} dari {$distributor}.",
        };
    }

    private function buildDiprosesMessage(ReturProduk $retur, string $produk, string $distributor): string
    {
        if ($retur->status === 'disetujui') {
            return "Retur disetujui oleh admin:\nDistributor: {$distributor}\nProduk: {$produk}\nJumlah: {$retur->jumlah_retur}\nStatus: Disetujui.";
        }

        if ($retur->status === 'ditolak') {
            return "Retur ditolak oleh admin:\nDistributor: {$distributor}\nProduk: {$produk}\nJumlah: {$retur->jumlah_retur}\nAlasan penolakan: {$retur->alasan_penolakan}.";
        }

        return "Retur diproses untuk {$produk} dari {$distributor}.";
    }
}

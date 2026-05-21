<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\JsonResponse;

class ProdukApiController extends Controller
{
    /**
     * Ambil batch aktif untuk produk tertentu.
     */
    public function batch(Produk $produk): JsonResponse
    {
        $batches = $produk->batches()
            ->select('batch.*')
            ->selectSub(
                'COALESCE((SELECT SUM(dsm.jumlah) FROM detail_stok_masuk dsm WHERE dsm.id_batch = batch.id_batch), 0)
                - COALESCE((SELECT SUM(dsk.jumlah) FROM detail_stok_keluar dsk WHERE dsk.id_batch = batch.id_batch), 0)',
                'stok_batch'
            )
            ->whereDate('tanggal_expired', '>=', today())
            ->orderBy('tanggal_expired')
            ->get()
            ->filter(fn ($batch): bool => (int) $batch->stok_batch > 0)
            ->values()
            ->map(fn ($batch): array => [
                'id_batch' => $batch->id_batch,
                'nomor_batch' => $batch->nomor_batch,
                'tanggal_expired' => $batch->tanggal_expired?->toDateString(),
                'tanggal_expired_formatted' => $batch->tanggal_expired?->locale('id')->translatedFormat('d F Y'),
                'stok_batch' => (int) $batch->stok_batch,
                'akan_expired' => $batch->tanggal_expired?->lte(today()->addDays(30)) ?? false,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Batch aktif berhasil diambil.',
            'data' => $batches,
        ]);
    }

    /**
     * Ambil stok terkini produk tertentu.
     */
    public function stok(Produk $produk): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Stok produk berhasil diambil.',
            'data' => [
                'id_produk' => $produk->id_produk,
                'nama_produk' => $produk->nama_produk,
                'stok_terkini' => $produk->stok_terkini,
                'stok_minimum' => $produk->stok_minimum,
            ],
        ]);
    }
}

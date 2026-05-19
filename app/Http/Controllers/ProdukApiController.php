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
            ->whereDate('tanggal_expired', '>=', today())
            ->whereHas('produk', fn ($query) => $query->where('stok_terkini', '>', 0))
            ->orderBy('tanggal_expired')
            ->get()
            ->map(fn ($batch): array => [
                'id_batch' => $batch->id_batch,
                'nomor_batch' => $batch->nomor_batch,
                'tanggal_expired' => $batch->tanggal_expired?->toDateString(),
                'tanggal_expired_formatted' => $batch->tanggal_expired?->locale('id')->translatedFormat('d F Y'),
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

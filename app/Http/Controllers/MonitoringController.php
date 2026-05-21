<?php

namespace App\Http\Controllers;

use App\Services\MonitoringService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * Buat controller monitoring baru.
     */
    public function __construct(private readonly MonitoringService $monitoringService) {}

    /**
     * Tampilkan dashboard monitoring stok.
     */
    public function index(): View
    {
        return view('monitoring.index', $this->monitoringService->dashboardData());
    }

    /**
     * Tampilkan daftar produk untuk monitoring.
     */
    public function products(Request $request): View
    {
        return view('monitoring.products', [
            'products' => $this->monitoringService->products($request->input('q')),
            'filters' => $request->only('q'),
        ]);
    }

    /**
     * Endpoint ringkasan dashboard monitoring.
     */
    public function summary(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Ringkasan monitoring berhasil diambil.',
            'data' => [
                ...$this->monitoringService->summary(),
                'updated_at' => now('Asia/Jakarta')->format('H:i:s'),
            ],
        ]);
    }

    /**
     * Endpoint data grafik stok tujuh hari terakhir.
     */
    public function chartData(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data grafik monitoring berhasil diambil.',
            'data' => $this->monitoringService->chartData(),
        ]);
    }
}

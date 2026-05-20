<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Services\AuthService;
use App\Services\DashboardAnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Buat controller dashboard baru.
     */
    public function __construct(
        private readonly AuthService $authService,
        private readonly DashboardAnalyticsService $dashboardAnalyticsService,
    ) {}

    /**
     * Arahkan pengguna ke dashboard sesuai role.
     */
    public function index(Request $request): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        return redirect()->route($this->authService->dashboardRoute($pengguna));
    }

    /**
     * Tampilkan dashboard administrator.
     */
    public function admin(): View
    {
        return $this->dashboardView('Administrator');
    }

    /**
     * Tampilkan dashboard staf gudang.
     */
    public function staf(): View
    {
        return $this->dashboardView('Staf Gudang');
    }

    /**
     * Tampilkan dashboard manajer.
     */
    public function manajer(): View
    {
        return $this->dashboardView('Manajer');
    }

    /**
     * Tampilkan dashboard umum saat role belum dikenali.
     */
    public function fallback(): View
    {
        return view('dashboard.role', [
            'title' => 'Dashboard',
            'description' => 'Role akun belum dikenali. Hubungi Administrator untuk memperbaiki hak akses akun.',
        ]);
    }

    /**
     * Endpoint KPI dashboard.
     */
    public function kpi(Request $request): JsonResponse
    {
        return $this->jsonData($this->dashboardAnalyticsService->kpi($request->query()));
    }

    /**
     * Endpoint grafik pergerakan stok.
     */
    public function stockMovement(Request $request): JsonResponse
    {
        return $this->jsonData($this->dashboardAnalyticsService->stockMovement($request->query()));
    }

    /**
     * Endpoint grafik status order.
     */
    public function orderStatus(Request $request): JsonResponse
    {
        return $this->jsonData($this->dashboardAnalyticsService->orderStatus($request->query()));
    }

    /**
     * Endpoint grafik produk terlaris.
     */
    public function topProducts(Request $request): JsonResponse
    {
        return $this->jsonData($this->dashboardAnalyticsService->topProducts($request->query()));
    }

    /**
     * Endpoint grafik distribusi per distributor.
     */
    public function distributorDistribution(Request $request): JsonResponse
    {
        return $this->jsonData($this->dashboardAnalyticsService->distributorDistribution($request->query()));
    }

    /**
     * Endpoint aktivitas terbaru dashboard.
     */
    public function latestActivities(): JsonResponse
    {
        return $this->jsonData($this->dashboardAnalyticsService->latestActivities());
    }

    /**
     * Tampilkan dashboard role.
     */
    private function dashboardView(string $role): View
    {
        $activities = $this->dashboardAnalyticsService->latestActivities();

        return view('dashboard.index', [
            'role' => $role,
            'showInventoryValue' => $role !== 'Staf Gudang',
            'showAllCharts' => $role !== 'Staf Gudang',
            'activities' => $activities,
            'warningProducts' => $role === 'Staf Gudang'
                ? DB::table('produk')
                    ->where(fn ($query) => $query->where('stok_terkini', 0)->orWhereColumn('stok_terkini', '<=', 'stok_minimum'))
                    ->orderBy('stok_terkini')
                    ->limit(5)
                    ->get()
                : collect(),
        ]);
    }

    /**
     * Format response JSON dashboard.
     *
     * @param  array<string, mixed>  $data
     */
    private function jsonData(array $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'generated_at' => now('Asia/Jakarta')->toDateTimeString(),
        ]);
    }
}

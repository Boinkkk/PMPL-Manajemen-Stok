<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Services\AuthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Buat controller dashboard baru.
     */
    public function __construct(private readonly AuthService $authService) {}

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
        return view('dashboard.role', [
            'title' => 'Dashboard Administrator',
            'description' => 'Akses penuh untuk manajemen pengguna, stok masuk, dan stok keluar.',
        ]);
    }

    /**
     * Tampilkan dashboard staf gudang.
     */
    public function staf(): View
    {
        return view('dashboard.role', [
            'title' => 'Dashboard Staf Gudang',
            'description' => 'Kelola pencatatan stok masuk dan stok keluar.',
        ]);
    }

    /**
     * Tampilkan dashboard manajer.
     */
    public function manajer(): View
    {
        return view('dashboard.role', [
            'title' => 'Dashboard Manajer',
            'description' => 'Pantau riwayat stok dan distribusi secara read-only.',
        ]);
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
}

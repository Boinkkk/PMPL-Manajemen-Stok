<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Services\AuthService;
use App\Services\PenggunaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Buat controller autentikasi baru.
     */
    public function __construct(
        private readonly AuthService $authService,
        private readonly PenggunaService $penggunaService,
    ) {}

    /**
     * Tampilkan halaman login.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Tampilkan halaman register publik untuk testing.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register', [
            'roles' => Role::query()->orderBy('nama_role')->get(),
        ]);
    }

    /**
     * Proses login pengguna.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $pengguna = $this->authService->login($request);

        return redirect()
            ->route($this->authService->dashboardRoute($pengguna))
            ->with('success', 'Login berhasil. Selamat datang kembali.');
    }

    /**
     * Proses register publik untuk testing.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $this->penggunaService->register($request);

        return redirect()
            ->route('login')
            ->with('success', 'Akun testing berhasil dibuat. Silakan login.');
    }

    /**
     * Proses logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()
            ->route('login')
            ->with('success', 'Logout berhasil.');
    }
}

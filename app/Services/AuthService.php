<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\Pengguna;
use App\Services\Concerns\AuditTrailTrait;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthService
{
    use AuditTrailTrait;

    /**
     * Buat service autentikasi baru.
     */
    public function __construct(
        private readonly AuthFactory $auth,
        private readonly Hasher $hasher,
    ) {}

    /**
     * Proses login pengguna, simpan session, dan catat audit trail.
     */
    public function login(LoginRequest $request): Pengguna
    {
        $request->ensureIsNotRateLimited();

        $pengguna = Pengguna::query()
            ->with('role')
            ->where('username', $request->string('username')->toString())
            ->first();

        if (! $pengguna instanceof Pengguna) {
            $request->hitRateLimiter();

            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        if ($pengguna->status !== 'aktif') {
            $request->hitRateLimiter();

            throw ValidationException::withMessages([
                'username' => 'Akun Anda telah dinonaktifkan. Hubungi Administrator.',
            ]);
        }

        if (! $this->hasher->check($request->string('password')->toString(), $pengguna->password)) {
            $request->hitRateLimiter();

            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        $this->auth->guard()->login($pengguna);
        $request->session()->regenerate();
        $request->session()->put('pengguna', [
            'id_pengguna' => $pengguna->id_pengguna,
            'nama_lengkap' => $pengguna->nama_lengkap,
            'role' => $pengguna->role?->nama_role,
        ]);

        $request->clearRateLimiter();

        $this->simpanAuditTrail(
            'LOGIN',
            'auth',
            null,
            $this->auditDataPengguna($pengguna),
            $request->ip(),
            $pengguna
        );

        return $pengguna;
    }

    /**
     * Proses logout pengguna dan bersihkan session.
     */
    public function logout(Request $request): void
    {
        $pengguna = $request->user();

        if ($pengguna instanceof Pengguna) {
            $this->simpanAuditTrail(
                'LOGOUT',
                'auth',
                $this->auditDataPengguna($pengguna),
                null,
                $request->ip(),
                $pengguna
            );
        }

        $this->auth->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Dapatkan nama route dashboard sesuai role pengguna.
     */
    public function dashboardRoute(Pengguna $pengguna): string
    {
        return match (true) {
            $pengguna->hasAnyRole(['Administrator']) => 'dashboard.admin',
            $pengguna->hasAnyRole(['Staf Gudang']) => 'dashboard.staf',
            $pengguna->hasAnyRole(['Manajer']) => 'dashboard.manajer',
            default => 'dashboard.fallback',
        };
    }

    /**
     * Ambil data aman pengguna untuk audit trail.
     *
     * @return array<string, mixed>
     */
    private function auditDataPengguna(Pengguna $pengguna): array
    {
        return $pengguna->only([
            'id_pengguna',
            'id_role',
            'nama_lengkap',
            'username',
            'email',
            'status',
        ]);
    }
}

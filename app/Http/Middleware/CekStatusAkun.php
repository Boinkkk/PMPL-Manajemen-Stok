<?php

namespace App\Http\Middleware;

use App\Models\Pengguna;
use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekStatusAkun
{
    /**
     * Buat middleware pengecekan status akun.
     */
    public function __construct(private readonly AuthService $authService) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pengguna = $request->user();

        if ($pengguna instanceof Pengguna && $pengguna->status !== 'aktif') {
            $this->authService->logout($request);

            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan.');
        }

        return $next($request);
    }
}

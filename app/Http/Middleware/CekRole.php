<?php

namespace App\Http\Middleware;

use App\Models\Pengguna;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $pengguna = $request->user();
        $allowedRoles = array_map(static fn (string $role): string => trim($role), $roles);

        if (! $pengguna instanceof Pengguna || ! $pengguna->hasAnyRole($allowedRoles)) {
            return redirect()
                ->route('dashboard')
                ->with('error', '403 - Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}

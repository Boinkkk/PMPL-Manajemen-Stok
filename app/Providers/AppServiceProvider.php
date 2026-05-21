<?php

namespace App\Providers;

use App\Models\Pengguna;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('lihat-stok', fn (Pengguna $pengguna): bool => $pengguna->hasAnyRole([
            'Administrator',
            'Staf Gudang',
            'Manajer',
        ]));

        Gate::define('kelola-stok', fn (Pengguna $pengguna): bool => $pengguna->canManageStock());

        Gate::define('hapus-stok', fn (Pengguna $pengguna): bool => $pengguna->isAdministrator());
    }
}

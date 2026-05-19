<?php

use App\Http\Controllers\StokKeluarController;
use App\Http\Controllers\StokMasukController;
use Illuminate\Support\Facades\Route;

Route::prefix('stok')->group(function (): void {
    Route::middleware('cek.role:Administrator,Staf Gudang')->group(function (): void {
        Route::get('stok-masuk/create', [StokMasukController::class, 'create'])
            ->name('stok-masuk.create');

        Route::post('stok-masuk', [StokMasukController::class, 'store'])
            ->name('stok-masuk.store');

        Route::get('stok-keluar/create', [StokKeluarController::class, 'create'])
            ->name('stok-keluar.create');

        Route::post('stok-keluar', [StokKeluarController::class, 'store'])
            ->name('stok-keluar.store');
    });

    Route::middleware('cek.role:Administrator')->group(function (): void {
        Route::delete('stok-masuk/{stokMasuk}', [StokMasukController::class, 'destroy'])
            ->name('stok-masuk.destroy');

        Route::delete('stok-keluar/{stokKeluar}', [StokKeluarController::class, 'destroy'])
            ->name('stok-keluar.destroy');
    });

    Route::middleware('cek.role:Administrator,Staf Gudang,Manajer')->group(function (): void {
        Route::get('stok-masuk', [StokMasukController::class, 'index'])
            ->name('stok-masuk.index');

        Route::get('stok-masuk/{stokMasuk}', [StokMasukController::class, 'show'])
            ->name('stok-masuk.show');

        Route::get('stok-keluar', [StokKeluarController::class, 'index'])
            ->name('stok-keluar.index');

        Route::get('stok-keluar/{stokKeluar}', [StokKeluarController::class, 'show'])
            ->name('stok-keluar.show');
    });
});

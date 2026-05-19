<?php

use App\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;

Route::middleware('cek.role:Administrator')
    ->prefix('pengguna')
    ->name('pengguna.')
    ->group(function (): void {
        Route::get('/', [PenggunaController::class, 'index'])->name('index');
        Route::get('/tambah', [PenggunaController::class, 'create'])->name('create');
        Route::post('/', [PenggunaController::class, 'store'])->name('store');

        Route::get('/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('edit');
        Route::put('/{pengguna}', [PenggunaController::class, 'update'])->name('update');

        Route::patch('/{pengguna}/nonaktifkan', [PenggunaController::class, 'nonaktifkan'])
            ->name('nonaktifkan');
    });

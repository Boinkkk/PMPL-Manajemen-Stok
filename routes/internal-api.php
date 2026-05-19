<?php

use App\Http\Controllers\ProdukApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware('cek.role:Administrator,Staf Gudang,Manajer')
    ->group(function (): void {
        Route::get('produk/{produk}/batch', [ProdukApiController::class, 'batch'])
            ->name('api.produk.batch');

        Route::get('produk/{produk}/stok', [ProdukApiController::class, 'stok'])
            ->name('api.produk.stok');
    });

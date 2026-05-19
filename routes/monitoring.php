<?php

use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\StokMinimumController;
use Illuminate\Support\Facades\Route;

Route::middleware('cek.role:Administrator,Staf Gudang,Manajer')
    ->prefix('monitoring')
    ->name('monitoring.')
    ->group(function (): void {
        Route::get('/', [MonitoringController::class, 'index'])->name('index');
        Route::get('/produk', [MonitoringController::class, 'products'])->name('products');
        Route::get('/summary', [MonitoringController::class, 'summary'])->name('summary');
        Route::get('/chart-data', [MonitoringController::class, 'chartData'])->name('chart-data');
    });

Route::middleware('cek.role:Administrator')
    ->prefix('monitoring/stok-minimum')
    ->name('monitoring.stok-minimum.')
    ->group(function (): void {
        Route::get('/', [StokMinimumController::class, 'index'])->name('index');
        Route::put('/', [StokMinimumController::class, 'update'])->name('update');
    });

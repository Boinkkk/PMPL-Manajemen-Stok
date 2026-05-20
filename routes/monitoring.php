<?php

use App\Http\Controllers\DataEoqController;
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
        Route::post('/eoq/update-stok-minimum', [DataEoqController::class, 'updateStokMinimum'])
            ->name('eoq.update-stok-minimum');
    });

Route::middleware('cek.role:Administrator,Staf Gudang,Manajer')
    ->prefix('monitoring/eoq')
    ->name('monitoring.eoq.')
    ->group(function (): void {
        Route::get('/', [DataEoqController::class, 'index'])->name('index');
    });

Route::middleware('cek.role:Administrator,Staf Gudang')
    ->prefix('monitoring/eoq')
    ->name('monitoring.eoq.')
    ->group(function (): void {
        Route::post('/sinkronisasi', [DataEoqController::class, 'sync'])->name('sync');
        Route::put('/{dataEoq}', [DataEoqController::class, 'update'])->name('update');
    });

Route::middleware('cek.role:Administrator')
    ->prefix('monitoring/stok-minimum')
    ->name('monitoring.stok-minimum.')
    ->group(function (): void {
        Route::get('/', [StokMinimumController::class, 'index'])->name('index');
        Route::put('/', [StokMinimumController::class, 'update'])->name('update');
    });

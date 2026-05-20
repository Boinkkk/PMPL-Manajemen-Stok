<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/dashboard/utama', [DashboardController::class, 'fallback'])
    ->name('dashboard.fallback');

Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
    ->middleware('cek.role:Administrator')
    ->name('dashboard.admin');

Route::get('/dashboard/staf', [DashboardController::class, 'staf'])
    ->middleware('cek.role:Staf Gudang')
    ->name('dashboard.staf');

Route::get('/dashboard/manajer', [DashboardController::class, 'manajer'])
    ->middleware('cek.role:Manajer')
    ->name('dashboard.manajer');

Route::middleware('cek.role:Administrator,Staf Gudang,Manajer')->group(function (): void {
    Route::get('/dashboard/kpi', [DashboardController::class, 'kpi'])->name('dashboard.kpi');
    Route::get('/dashboard/grafik/pergerakan-stok', [DashboardController::class, 'stockMovement'])->name('dashboard.grafik.pergerakan-stok');
    Route::get('/dashboard/grafik/order-status', [DashboardController::class, 'orderStatus'])->name('dashboard.grafik.order-status');
    Route::get('/dashboard/grafik/produk-terlaris', [DashboardController::class, 'topProducts'])->name('dashboard.grafik.produk-terlaris');
    Route::get('/dashboard/grafik/distribusi-distributor', [DashboardController::class, 'distributorDistribution'])->name('dashboard.grafik.distribusi-distributor');
    Route::get('/dashboard/aktivitas-terbaru', [DashboardController::class, 'latestActivities'])->name('dashboard.aktivitas-terbaru');
});

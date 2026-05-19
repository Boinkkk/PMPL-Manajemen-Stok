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

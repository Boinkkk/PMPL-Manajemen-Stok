<?php

use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::prefix('laporan')
    ->name('laporan.')
    ->middleware('cek.role:Administrator,Manajer')
    ->group(function (): void {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
    });

Route::prefix('laporan')
    ->name('laporan.')
    ->middleware('cek.role:Administrator,Staf Gudang,Manajer')
    ->group(function (): void {
        Route::get('/{type}', [LaporanController::class, 'show'])->name('show');
        Route::get('/{type}/excel', [LaporanController::class, 'excel'])->middleware('cek.role:Administrator,Manajer')->name('excel');
        Route::get('/{type}/pdf', [LaporanController::class, 'pdf'])->middleware('cek.role:Administrator,Manajer')->name('pdf');
        Route::get('/{type}/print', [LaporanController::class, 'print'])->middleware('cek.role:Administrator,Manajer')->name('print');
    });

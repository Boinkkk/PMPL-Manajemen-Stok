<?php

use App\Http\Controllers\DistributorController;
use Illuminate\Support\Facades\Route;

Route::middleware('cek.role:Administrator,Staf Gudang')->group(function (): void {
    Route::get('distributor/check-duplicate', [DistributorController::class, 'checkDuplicate'])->name('distributor.check-duplicate');
    Route::resource('distributor', DistributorController::class)->except(['destroy']);
});

Route::middleware('cek.role:Administrator')->group(function (): void {
    Route::get('distributor-export', [DistributorController::class, 'export'])->name('distributor.export');
    Route::delete('distributor/{distributor}', [DistributorController::class, 'destroy'])->name('distributor.destroy');
});

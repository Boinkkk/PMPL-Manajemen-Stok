<?php

use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware('cek.role:Administrator,Staf Gudang')->group(function (): void {
    Route::get('supplier/check-duplicate', [SupplierController::class, 'checkDuplicate'])->name('supplier.check-duplicate');
    Route::resource('supplier', SupplierController::class)->except(['destroy']);
});

Route::middleware('cek.role:Administrator')->group(function (): void {
    Route::get('supplier-export', [SupplierController::class, 'export'])->name('supplier.export');
    Route::delete('supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
});

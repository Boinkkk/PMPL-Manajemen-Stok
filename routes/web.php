<?php

use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SupplierController::class, 'index'])->name('home');

Route::redirect('/staff-gudang', '/staff-gudang/supplier')->name('staff-gudang.dashboard');
Route::get('/staff-gudang/supplier', [SupplierController::class, 'staffGudang'])->name('staff-gudang.supplier.index');
Route::get('/supplier/produk/all', [SupplierController::class, 'getAllProduk'])->name('supplier.produk.all');
Route::resource('supplier', SupplierController::class)->whereNumber('supplier');

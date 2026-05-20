<?php

use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SupplierController::class, 'index'])->name('home');

Route::get('/supplier/produk/all', [SupplierController::class, 'getAllProduk'])->name('supplier.produk.all');
Route::resource('supplier', SupplierController::class)->whereNumber('supplier');

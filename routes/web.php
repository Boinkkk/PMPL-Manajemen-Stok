<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SatuanController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/kategori');

Route::resource('kategori', KategoriController::class)->except(['show']);
Route::resource('satuan', SatuanController::class)->except(['show']);

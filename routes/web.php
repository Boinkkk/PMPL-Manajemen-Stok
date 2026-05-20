<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

require __DIR__.'/auth.php';

Route::middleware(['auth', 'cek.status'])->group(function (): void {
    require __DIR__.'/dashboard.php';
    require __DIR__.'/monitoring.php';
    require __DIR__.'/notifikasi.php';
    require __DIR__.'/laporan.php';
    require __DIR__.'/pengguna.php';
    require __DIR__.'/order-distribusi.php';
    require __DIR__.'/stok.php';
    require __DIR__.'/supplier.php';
    require __DIR__.'/distributor.php';
    require __DIR__.'/internal-api.php';
    require __DIR__.'/data-produk.php';
    require __DIR__.'/audit.php';
});

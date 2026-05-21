<?php

use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

Route::middleware('cek.role:Administrator,Staf Gudang,Manajer')
    ->prefix('notifikasi')
    ->name('notifikasi.')
    ->group(function (): void {
        Route::get('/', [NotifikasiController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotifikasiController::class, 'unreadCount'])->name('unread-count');
        Route::get('/preview', [NotifikasiController::class, 'preview'])->name('preview');
        Route::patch('/baca-semua', [NotifikasiController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/bulk', [NotifikasiController::class, 'bulkAction'])->name('bulk');
        Route::patch('/{notifikasi}/baca', [NotifikasiController::class, 'markAsRead'])->whereNumber('notifikasi')->name('mark-read');
        Route::delete('/{notifikasi}', [NotifikasiController::class, 'destroy'])->whereNumber('notifikasi')->name('destroy');
    });

<?php

use App\Http\Controllers\OrderDistribusiController;
use Illuminate\Support\Facades\Route;

Route::prefix('order-distribusi')
    ->name('order-distribusi.')
    ->group(function (): void {
        Route::middleware('cek.role:Administrator,Staf Gudang,Manajer')->group(function (): void {
            Route::get('/', [OrderDistribusiController::class, 'index'])->name('index');
            Route::get('/export', [OrderDistribusiController::class, 'export'])
                ->middleware('cek.role:Administrator,Manajer')
                ->name('export');
            Route::get('/{orderDistribusi}', [OrderDistribusiController::class, 'show'])->whereNumber('orderDistribusi')->name('show');
            Route::get('/{orderDistribusi}/cetak', [OrderDistribusiController::class, 'print'])->whereNumber('orderDistribusi')->name('print');
        });

        Route::middleware('cek.role:Administrator,Staf Gudang')->group(function (): void {
            Route::get('/tambah', [OrderDistribusiController::class, 'create'])->name('create');
            Route::post('/', [OrderDistribusiController::class, 'store'])->name('store');
            Route::get('/{orderDistribusi}/edit', [OrderDistribusiController::class, 'edit'])->whereNumber('orderDistribusi')->name('edit');
            Route::put('/{orderDistribusi}', [OrderDistribusiController::class, 'update'])->whereNumber('orderDistribusi')->name('update');
            Route::post('/{orderDistribusi}/setujui', [OrderDistribusiController::class, 'approve'])->whereNumber('orderDistribusi')->name('approve');
            Route::post('/{orderDistribusi}/tolak', [OrderDistribusiController::class, 'reject'])->whereNumber('orderDistribusi')->name('reject');
            Route::post('/{orderDistribusi}/batalkan', [OrderDistribusiController::class, 'cancel'])->whereNumber('orderDistribusi')->name('cancel');
            Route::post('/{orderDistribusi}/buat-ulang', [OrderDistribusiController::class, 'reorder'])->whereNumber('orderDistribusi')->name('reorder');
        });
    });

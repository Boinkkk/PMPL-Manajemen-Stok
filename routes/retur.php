<?php


use App\Http\Controllers\ReturController;
use Illuminate\Support\Facades\Route;


// ===== MODUL RETUR (Tanpa Auth - Sementara) =====
// TODO: Tambahkan middleware('auth') ke group ini setelah login diintegrasikan
Route::prefix('retur')->group(function () {
    Route::get('/', [ReturController::class, 'index'])->name('retur.index');
    Route::get('create', [ReturController::class, 'create'])->name('retur.create');
    Route::post('/', [ReturController::class, 'store'])->name('retur.store');
    Route::get('{retur}', [ReturController::class, 'show'])->name('retur.show');
    Route::post('{retur}/setujui', [ReturController::class, 'setujui'])->name('retur.setujui');
    Route::post('{retur}/tolak', [ReturController::class, 'tolak'])->name('retur.tolak');
    Route::delete('{retur}', [ReturController::class, 'destroy'])->name('retur.destroy');
});

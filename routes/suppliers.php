<?php

use App\Http\Controllers\Suppliers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('suppliers')->name('suppliers.')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::get('/data', [SupplierController::class, 'data'])->name('data');
    Route::get('/create', [SupplierController::class, 'create'])->name('create');
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
}); 
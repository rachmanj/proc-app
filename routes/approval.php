<?php

use App\Http\Controllers\Approvals\POController;
use Illuminate\Support\Facades\Route;

Route::prefix('approvals')->name('approvals.')->group(function () {
    Route::prefix('po')->name('po.')->group(function () {
        Route::get('/', [POController::class, 'index'])->name('index');
        Route::get('/search', [POController::class, 'search'])->name('search');
        Route::get('/pending', [POController::class, 'pending'])->name('pending');
        Route::get('/pending-data', [POController::class, 'pendingData'])->name('pending-data');
        Route::get('/{purchaseOrder}/attachments', [POController::class, 'getAttachments'])->name('get-attachments');
        Route::get('/{purchaseOrder}', [POController::class, 'show'])->name('show');
    });
});

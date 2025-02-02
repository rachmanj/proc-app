<?php

use App\Http\Controllers\Procurement\POController;
use Illuminate\Support\Facades\Route;

Route::prefix('procurement')->name('procurement.')->group(function () {
    Route::prefix('po')->name('po.')->group(function () {
        Route::get('/', [POController::class, 'index'])->name('index');
        Route::get('/search', [POController::class, 'search'])->name('search');
        Route::post('/store', [POController::class, 'store'])->name('store');
        Route::get('/data', [POController::class, 'data'])->name('data');
        Route::get('/{purchaseOrder}/edit', [POController::class, 'edit'])->name('edit');
        Route::put('/{purchaseOrder}', [POController::class, 'update'])->name('update');
        Route::get('/{purchaseOrder}/attachments', [POController::class, 'getAttachments'])->name('get-attachments');
        Route::post('/{purchaseOrder}/upload-attachments', [POController::class, 'uploadAttachments'])->name('upload-attachments');
        Route::delete('/detach-file/{attachmentId}', [POController::class, 'detachFile'])->name('detach-file');
        Route::get('/{purchaseOrder}', [POController::class, 'show'])->name('show');
        Route::delete('/{purchaseOrder}', [POController::class, 'destroy'])->name('destroy');
    });
});

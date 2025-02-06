<?php

use App\Http\Controllers\Procurement\POController;
use App\Http\Controllers\Procurement\PRController;
use App\Http\Controllers\PurchaseOrderApprovalController;
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

        // Approval Routes
        Route::post('/{purchaseOrder}/submit', [PurchaseOrderApprovalController::class, 'submit'])->name('submit');
        Route::post('/{purchaseOrderApproval}/approve', [PurchaseOrderApprovalController::class, 'approve'])->name('approve');
        Route::post('/{purchaseOrder}/reject', [PurchaseOrderApprovalController::class, 'reject'])->name('reject');
        Route::post('/{purchaseOrder}/revise', [PurchaseOrderApprovalController::class, 'revise'])->name('revise');
    });

    Route::prefix('pr')->name('pr.')->group(function () {
        Route::get('/', [PRController::class, 'index'])->name('index');
        Route::get('/search', [PRController::class, 'searchPage'])->name('procurement.pr.search.page');
        Route::get('/search-data', [PRController::class, 'search'])->name('search');
        Route::get('/data', [PRController::class, 'data'])->name('data');
        Route::get('/{purchaseRequest}', [PRController::class, 'show'])->name('show');
        Route::get('/{purchaseRequest}/view', [PRController::class, 'show'])->name('view');
        Route::get('/clear-search', [PRController::class, 'clearSearch'])->name('clear-search');
    });
});

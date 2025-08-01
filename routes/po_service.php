<?php

use App\Http\Controllers\PoService\PoServiceController;
use App\Http\Controllers\PoService\ItemServiceController;
use Illuminate\Support\Facades\Route;


// PO Service Routes
Route::prefix('po-service')->group(function () {
    // Route untuk ItemServiceController DULUAN!
    Route::get('/items-data', [ItemServiceController::class, 'data'])->name('item_service.data');
    Route::post('/store', [ItemServiceController::class, 'store'])->name('item_service.store');
    Route::delete('/{po_service}/delete-all', [ItemServiceController::class, 'deleteAll'])->name('item_service.delete_all');
    Route::post('/import-item', [ItemServiceController::class, 'importItem'])->name('item_service.import_item');
    Route::delete('/destroy/{id}', [ItemServiceController::class, 'destroy'])->name('item_service.destroy');
    Route::get('/edit/{id}', [ItemServiceController::class, 'edit'])->name('item_service.edit');
    Route::put('/update/{id}', [ItemServiceController::class, 'update'])->name('item_service.update');

    // Baru route PoServiceController yang pakai parameter
    Route::get('/', [PoServiceController::class, 'index'])->name('po_service.index');
    Route::get('/create', [PoServiceController::class, 'create'])->name('po_service.create');
    Route::post('/', [PoServiceController::class, 'store'])->name('po_service.store');
    Route::get('/data', [PoServiceController::class, 'data'])->name('po_service.data');
    Route::get('/{po_service}', [PoServiceController::class, 'show'])->name('po_service.show');
    Route::get('/{po_service}/edit', [PoServiceController::class, 'edit'])->name('po_service.edit');
    Route::put('/{po_service}', [PoServiceController::class, 'update'])->name('po_service.update');
    Route::delete('/{po_service}', [PoServiceController::class, 'destroy'])->name('po_service.destroy');
    Route::get('/{po_service}/add-items', [PoServiceController::class, 'addItems'])->name('po_service.add_items');
    Route::post('/{po_service}/add-items', [PoServiceController::class, 'storeItems'])->name('po_service.store_items');
    Route::get('/{po_service}/edit-items', [PoServiceController::class, 'editItems'])->name('po_service.edit_items');
    Route::put('/{po_service}/edit-items', [PoServiceController::class, 'updateItems'])->name('po_service.update_items');
    Route::delete('/{po_service}/delete-items', [PoServiceController::class, 'deleteItems'])->name('po_service.delete_items');
    Route::get('/{po_service}/preview', [PoServiceController::class, 'preview'])->name('po_service.preview');
    Route::get('/{po_service}/print-pdf', [PoServiceController::class, 'printPdf'])->name('po_service.print_pdf');
    Route::get('/{po_service}/summary', [PoServiceController::class, 'summary'])->name('po_service.summary');
});
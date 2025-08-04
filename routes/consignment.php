<?php

use App\Http\Controllers\Consignment\ConsignmentController;
use App\Http\Controllers\Consignment\ImportController;
use App\Http\Controllers\Consignment\ItemPriceController;
use App\Http\Controllers\Consignment\WarehouseController;
use Illuminate\Support\Facades\Route;

// All routes are under the 'consignment' prefix and require the 'access_consignment' permission
Route::middleware(['permission:access_consignment'])->prefix('consignment')->name('consignment.')->group(function () {
    // Dashboard
    Route::get('/', [ConsignmentController::class, 'index'])->name('dashboard');

    // Item Prices
    Route::controller(ItemPriceController::class)->group(function () {
        Route::get('/item-prices', 'index')->name('item-prices.index');
        Route::get('/item-prices/create', 'create')->name('item-prices.create')->middleware('permission:crud_consignment');
        Route::post('/item-prices', 'store')->name('item-prices.store')->middleware('permission:crud_consignment');
        Route::get('/item-prices/{id}', 'show')->name('item-prices.show');
        Route::delete('/item-prices/{id}', 'destroy')->name('item-prices.destroy')->middleware('permission:crud_consignment');

        // Search
        Route::get('/search', 'search')->name('search')->middleware('permission:search_consignment');
        Route::get('/history/{itemCode}', 'history')->name('history')->middleware('permission:search_consignment');
    });

    // Warehouses
    Route::controller(WarehouseController::class)->group(function () {
        Route::get('/warehouses', 'index')->name('warehouses.index');
        Route::get('/warehouses/create', 'create')->name('warehouses.create')->middleware('permission:crud_consignment');
        Route::post('/warehouses', 'store')->name('warehouses.store')->middleware('permission:crud_consignment');
        Route::get('/warehouses/{id}', 'show')->name('warehouses.show');
        Route::get('/warehouses/{id}/edit', 'edit')->name('warehouses.edit')->middleware('permission:crud_consignment');
        Route::put('/warehouses/{id}', 'update')->name('warehouses.update')->middleware('permission:crud_consignment');
        Route::delete('/warehouses/{id}', 'destroy')->name('warehouses.destroy')->middleware('permission:crud_consignment');
    });

    // Import/Export
    Route::controller(ImportController::class)->group(function () {
        Route::get('/imports/upload', 'showUploadForm')->name('imports.upload')->middleware('permission:upload_consignment');
        Route::post('/imports/upload', 'processUpload')->name('imports.process')->middleware('permission:upload_consignment');
        Route::get('/imports/template', 'downloadTemplate')->name('imports.template');
        Route::get('/imports/status/{batch}', 'showStatus')->name('imports.status')->middleware('permission:upload_consignment');
        Route::post('/imports/process/{batch}', 'processBatch')->name('imports.process-batch')->middleware('permission:upload_consignment');
    });
});

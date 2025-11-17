<?php

use App\Http\Controllers\Master\DailyPRController;
use App\Http\Controllers\Master\POTempController;
use App\Http\Controllers\Master\SyncWithSapController;
use Illuminate\Support\Facades\Route;


Route::prefix('master')->name('master.')->group(function () {
    Route::prefix('sync-with-sap')->name('sync-with-sap.')->group(function () {
        Route::get('/', [SyncWithSapController::class, 'index'])->name('index');
        Route::post('sync-pr', [SyncWithSapController::class, 'syncPr'])->name('sync-pr');
        Route::post('sync-po', [SyncWithSapController::class, 'syncPo'])->name('sync-po');
        Route::post('truncate-pr', [SyncWithSapController::class, 'truncatePrTemp'])->name('truncate-pr');
        Route::post('truncate-po', [SyncWithSapController::class, 'truncatePoTemp'])->name('truncate-po');
    });

    Route::prefix('dailypr')->name('dailypr.')->group(function () {
        Route::get('/', [DailyPRController::class, 'index'])->name('index');
        Route::post('import', [DailyPRController::class, 'import'])->name('import');
        Route::post('sync-from-sap', [DailyPRController::class, 'syncFromSap'])->name('sync-from-sap');
        Route::get('data', [DailyPRController::class, 'data'])->name('data');
        Route::post('import-to-pr', [DailyPRController::class, 'importToPRTable'])->name('import-to-pr');
    });

    Route::prefix('potemp')->name('potemp.')->group(function () {
        Route::get('/', [POTempController::class, 'index'])->name('index');
        Route::post('import', [POTempController::class, 'import'])->name('import');
        Route::post('sync-from-sap', [POTempController::class, 'syncFromSap'])->name('sync-from-sap');
        Route::get('/data', [POTempController::class, 'data'])->name('data');
        Route::post('copy-to-po', [POTempController::class, 'copyToPO'])->name('copy-to-po');
    });
});

<?php

use App\Http\Controllers\Master\DailyPRController;
use App\Http\Controllers\Master\POTempController;
use Illuminate\Support\Facades\Route;


Route::prefix('master')->name('master.')->group(function () {
    Route::prefix('dailypr')->name('dailypr.')->group(function () {
        Route::get('/', [DailyPRController::class, 'index'])->name('index');
        Route::post('import', [DailyPRController::class, 'import'])->name('import');
        Route::get('data', [DailyPRController::class, 'data'])->name('data');
        Route::post('import-to-pr', [DailyPRController::class, 'importToPRTable'])->name('import-to-pr');
    });

    Route::prefix('potemp')->name('potemp.')->group(function () {
        Route::get('/', [POTempController::class, 'index'])->name('index');
        Route::post('import', [POTempController::class, 'import'])->name('import');
        Route::get('/data', [POTempController::class, 'data'])->name('data');
        Route::post('copy-to-po', [POTempController::class, 'copyToPO'])->name('copy-to-po');
    });
});

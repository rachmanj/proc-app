<?php

use App\Http\Controllers\Master\DailyPRController;
use Illuminate\Support\Facades\Route;

Route::prefix('master')->name('master.')->group(function () {
    Route::prefix('dailypr')->name('dailypr.')->group(function () {
        Route::get('/', [DailyPRController::class, 'index'])->name('index');
    });
});

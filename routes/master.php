<?php

use App\Http\Controllers\Master\DailyPRController;
use Illuminate\Support\Facades\Route;

Route::get('test-route', function () {
    $routes = Route::getRoutes();
    $routeList = [];

    foreach ($routes as $route) {
        $routeList[] = [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName()
        ];
    }

    return response()->json($routeList);
})->name('test.routes');

Route::prefix('master')->name('master.')->group(function () {
    Route::prefix('dailypr')->name('dailypr.')->group(function () {
        Route::get('/', [DailyPRController::class, 'index'])->name('index');
        Route::post('import', [DailyPRController::class, 'import'])->name('import');
        Route::get('data', [DailyPRController::class, 'data'])->name('data');
        Route::post('import-to-pr', [DailyPRController::class, 'importToPRTable'])->name('import-to-pr');
    });
});

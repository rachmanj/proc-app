<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\PurchaseOrderApprovalController;

use App\Http\Controllers\PoServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'index')->name('login');
        Route::post('/login', 'authenticate')->name('authenticate');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'index')->name('register');
        Route::post('/register', 'store')->name('register.store');
    });
});

// middleware('auth') means that the user must be authenticated to access the route
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('test', [TestController::class, 'index']);



    require __DIR__ . '/admin.php';
    require __DIR__ . '/procurement.php';
    require __DIR__ . '/master.php';
    require __DIR__ . '/approval.php';
    require __DIR__ . '/suppliers.php';
    require __DIR__ . '/po_service.php';
    require __DIR__ . '/consignment.php';
});

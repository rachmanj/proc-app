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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('test', [TestController::class, 'index']);

    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/metrics', [DashboardController::class, 'metrics'])->name('metrics');
        Route::get('/charts/pr-status', [DashboardController::class, 'prStatusChart'])->name('charts.pr-status');
        Route::get('/charts/po-trend', [DashboardController::class, 'poTrendChart'])->name('charts.po-trend');
        Route::get('/charts/approval-time', [DashboardController::class, 'approvalTimeChart'])->name('charts.approval-time');
        Route::get('/charts/top-suppliers', [DashboardController::class, 'topSuppliersChart'])->name('charts.top-suppliers');
        Route::get('/charts/department-pr', [DashboardController::class, 'departmentPrChart'])->name('charts.department-pr');
        Route::get('/activity', [DashboardController::class, 'activity'])->name('activity');
    });

    Route::prefix('api/notifications')->name('api.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });



    require __DIR__ . '/admin.php';
    require __DIR__ . '/procurement.php';
    require __DIR__ . '/master.php';
    require __DIR__ . '/approval.php';
    require __DIR__ . '/suppliers.php';
    require __DIR__ . '/po_service.php';
    require __DIR__ . '/consignment.php';
    require __DIR__ . '/reports.php';
});

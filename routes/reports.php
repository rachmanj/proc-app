<?php

use App\Http\Controllers\Reports\ReportsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:akses_report'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportsController::class, 'index'])->name('index');
    
    // PR Reports
    Route::prefix('pr')->name('pr.')->group(function () {
        Route::get('/status', [ReportsController::class, 'prStatus'])->name('status');
        Route::get('/aging', [ReportsController::class, 'prAging'])->name('aging');
        Route::get('/by-department', [ReportsController::class, 'prByDepartment'])->name('by-department');
        Route::get('/by-project', [ReportsController::class, 'prByProject'])->name('by-project');
        Route::get('/approval-time', [ReportsController::class, 'prApprovalTime'])->name('approval-time');
    });
    
    // PO Reports
    Route::prefix('po')->name('po.')->group(function () {
        Route::get('/status', [ReportsController::class, 'poStatus'])->name('status');
        Route::get('/value', [ReportsController::class, 'poValue'])->name('value');
        Route::get('/by-supplier', [ReportsController::class, 'poBySupplier'])->name('by-supplier');
        Route::get('/delivery-status', [ReportsController::class, 'poDeliveryStatus'])->name('delivery-status');
    });
    
    // Approval Reports
    Route::prefix('approval')->name('approval.')->group(function () {
        Route::get('/time-analysis', [ReportsController::class, 'approvalTimeAnalysis'])->name('time-analysis');
        Route::get('/by-approver', [ReportsController::class, 'approvalByApprover'])->name('by-approver');
        Route::get('/bottleneck', [ReportsController::class, 'approvalBottleneck'])->name('bottleneck');
    });
    
    // Export endpoints
    Route::post('/export/{type}', [ReportsController::class, 'export'])->name('export');
});


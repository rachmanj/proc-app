<?php

use App\Http\Controllers\Procurement\POController;
use App\Http\Controllers\Procurement\PRController;
use App\Http\Controllers\PurchaseOrderApprovalController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CollaborationController;
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
        Route::post('/update-attachment/{attachmentId}', [POController::class, 'updateAttachment'])->name('update-attachment');
        Route::delete('/detach-file/{attachmentId}', [POController::class, 'detachFile'])->name('detach-file');
        Route::get('/attachments/{attachmentId}/view', [POController::class, 'viewAttachment'])->name('view-attachment');
        Route::get('/attachments/{attachmentId}/preview-excel', [POController::class, 'previewExcel'])->name('preview-excel');
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
        Route::get('/{purchaseRequest}/edit', [PRController::class, 'edit'])->name('edit');
        Route::put('/{purchaseRequest}', [PRController::class, 'update'])->name('update');
        Route::get('/clear-search', [PRController::class, 'clearSearch'])->name('clear-search');

        // Attachment routes
        Route::get('/{purchaseRequest}/attachments', [PRController::class, 'getAttachments'])->name('get-attachments');
        Route::post('/{purchaseRequest}/upload-attachments', [PRController::class, 'uploadAttachments'])->name('upload-attachments');
        Route::post('/attachments/{attachment}', [PRController::class, 'updateAttachment'])->name('update-attachment');
        Route::delete('/attachments/{attachment}', [PRController::class, 'detachFile'])->name('detach-file');
        Route::get('/attachments/{attachment}/view', [PRController::class, 'viewAttachment'])->name('view-attachment');
        Route::get('/attachments/{attachment}/preview-excel', [PRController::class, 'previewExcel'])->name('preview-excel');
    });

    Route::prefix('comments')->name('comments.')->group(function () {
        // Specific routes first (before parameterized routes)
        Route::get('/users/search', [CommentController::class, 'searchUsers'])->name('users.search');
        Route::get('/comment/{id}', [CommentController::class, 'show'])->name('show');
        Route::post('/comment/{id}/update', [CommentController::class, 'update'])->name('update');
        Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->name('destroy');
        Route::post('/comment/{id}/resolve', [CommentController::class, 'toggleResolve'])->name('toggle-resolve');
        Route::post('/comment/{id}/pin', [CommentController::class, 'togglePin'])->name('toggle-pin');
        
        // Parameterized routes last
        Route::get('/{type}/{id}', [CommentController::class, 'index'])->name('index');
        Route::get('/{type}/{id}/counts', [CommentController::class, 'getCommentCounts'])->name('counts');
        Route::get('/{type}/{id}/line-item/{lineItemId}', [CommentController::class, 'getLineItemComments'])->name('line-item');
        Route::post('/{type}/{id}', [CommentController::class, 'store'])->name('store');
    });

    Route::prefix('activity')->name('activity.')->group(function () {
        Route::get('/{type}/{id}', [ActivityController::class, 'index'])->name('index');
        Route::get('/{type}/{id}/events', [ActivityController::class, 'getEvents'])->name('events');
        Route::get('/{type}/{id}/users', [ActivityController::class, 'getUsers'])->name('users');
    });

    Route::prefix('collaboration')->name('collaboration.')->group(function () {
        Route::get('/watchlist', [CollaborationController::class, 'myWatchlist'])->name('watchlist');
        Route::get('/buyers', [CollaborationController::class, 'getBuyers'])->name('buyers');
        Route::post('/{type}/{id}/assign', [CollaborationController::class, 'assign'])->name('assign');
        Route::delete('/{type}/{id}/unassign/{userId}', [CollaborationController::class, 'unassign'])->name('unassign');
        Route::get('/{type}/{id}/assignments', [CollaborationController::class, 'getAssignments'])->name('assignments');
        Route::post('/{type}/{id}/follow', [CollaborationController::class, 'follow'])->name('follow');
        Route::delete('/{type}/{id}/follow', [CollaborationController::class, 'unfollow'])->name('unfollow');
        Route::get('/{type}/{id}/follow-status', [CollaborationController::class, 'getFollowStatus'])->name('follow-status');
    });
});

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PrAttachment;
use App\Observers\PrAttachmentObserver;
use App\Models\PurchaseOrder;
use App\Observers\PurchaseOrderObserver;
use App\Models\PurchaseRequest;
use App\Observers\PurchaseRequestObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PrAttachment::observe(PrAttachmentObserver::class);
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        PurchaseRequest::observe(PurchaseRequestObserver::class);
    }
}

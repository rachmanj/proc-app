<?php

namespace App\Observers;

use App\Models\PurchaseRequest;
use Carbon\Carbon;

class PurchaseRequestObserver
{
    /**
     * Handle the PurchaseRequest "created" event.
     */
    public function created(PurchaseRequest $purchaseRequest): void
    {
        //
    }

    /**
     * Handle the PurchaseRequest "updating" event.
     */
    public function updating(PurchaseRequest $purchaseRequest): void
    {
        // Check if pr_status is being changed to 'approved'
        if ($purchaseRequest->isDirty('pr_status') && $purchaseRequest->pr_status === 'approved') {
            // Calculate and save the final day value before status changes to approved
            if ($purchaseRequest->generated_date) {
                $generatedDate = Carbon::parse($purchaseRequest->generated_date);
                $today = Carbon::today();
                $finalDayValue = $generatedDate->diffInDays($today);
                
                // Save the final day value to the day column
                $purchaseRequest->day = $finalDayValue;
            }
        }
    }

    /**
     * Handle the PurchaseRequest "updated" event.
     */
    public function updated(PurchaseRequest $purchaseRequest): void
    {
        //
    }

    /**
     * Handle the PurchaseRequest "deleted" event.
     */
    public function deleted(PurchaseRequest $purchaseRequest): void
    {
        //
    }

    /**
     * Handle the PurchaseRequest "restored" event.
     */
    public function restored(PurchaseRequest $purchaseRequest): void
    {
        //
    }

    /**
     * Handle the PurchaseRequest "force deleted" event.
     */
    public function forceDeleted(PurchaseRequest $purchaseRequest): void
    {
        //
    }
}

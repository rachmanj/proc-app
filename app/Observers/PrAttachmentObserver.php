<?php

namespace App\Observers;

use App\Models\PrAttachment;
use App\Models\PurchaseRequest;

class PrAttachmentObserver
{
    /**
     * Handle the PrAttachment "created" event.
     */
    public function created(PrAttachment $prAttachment): void
    {
        // Update pr_status to 'progress' for all purchase requests associated with this attachment
        $prAttachment->purchaseRequests()->update(['pr_status' => 'progress']);
        
        // Alternative approach: Update by pr_no if available
        if ($prAttachment->pr_no) {
            PurchaseRequest::where('pr_no', $prAttachment->pr_no)
                ->update(['pr_status' => 'progress']);
        }
    }

    /**
     * Handle the PrAttachment "updated" event.
     */
    public function updated(PrAttachment $prAttachment): void
    {
        //
    }

    /**
     * Handle the PrAttachment "deleted" event.
     */
    public function deleted(PrAttachment $prAttachment): void
    {
        // Check if PR still has other attachments
        $prAttachment->purchaseRequests()->each(function($purchaseRequest) {
            if ($purchaseRequest->attachments()->count() == 0) {
                // If no more attachments, revert status to 'OPEN'
                $purchaseRequest->update(['pr_status' => 'OPEN']);
            }
        });
    }

    /**
     * Handle the PrAttachment "restored" event.
     */
    public function restored(PrAttachment $prAttachment): void
    {
        //
    }

    /**
     * Handle the PrAttachment "force deleted" event.
     */
    public function forceDeleted(PrAttachment $prAttachment): void
    {
        //
    }
}

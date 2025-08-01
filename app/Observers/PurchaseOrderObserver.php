<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Log;

class PurchaseOrderObserver
{
    /**
     * Handle the PurchaseOrder "created" event.
     */
    public function created(PurchaseOrder $purchaseOrder): void
    {
        //
    }

    /**
     * Handle the PurchaseOrder "updating" event.
     */
    public function updating(PurchaseOrder $purchaseOrder): void
    {
        // Check if status is being changed to 'approved'
        if ($purchaseOrder->isDirty('status') && $purchaseOrder->status === PurchaseOrder::STATUS_APPROVED) {
            // Calculate and save the final day value before status changes to approved
            if ($purchaseOrder->create_date) {
                $createDate = \Carbon\Carbon::parse($purchaseOrder->create_date);
                $today = \Carbon\Carbon::today();
                $finalDayValue = $createDate->diffInDays($today);
                
                // Save the final day value to the day column
                $purchaseOrder->day = $finalDayValue;
            }
        }
    }

    /**
     * Handle the PurchaseOrder "updated" event.
     */
    public function updated(PurchaseOrder $purchaseOrder): void
    {
        // Check if the status was changed to 'approved'
        if ($purchaseOrder->isDirty('status') && $purchaseOrder->status === PurchaseOrder::STATUS_APPROVED) {
            $this->checkAndUpdatePrStatus($purchaseOrder);
        }
    }

    /**
     * Handle the PurchaseOrder "deleted" event.
     */
    public function deleted(PurchaseOrder $purchaseOrder): void
    {
        //
    }

    /**
     * Handle the PurchaseOrder "restored" event.
     */
    public function restored(PurchaseOrder $purchaseOrder): void
    {
        //
    }

    /**
     * Handle the PurchaseOrder "force deleted" event.
     */
    public function forceDeleted(PurchaseOrder $purchaseOrder): void
    {
        //
    }

    /**
     * Check if all purchase orders for the same pr_no are approved
     * and update pr_status to 'approved' if they are
     */
    private function checkAndUpdatePrStatus(PurchaseOrder $purchaseOrder): void
    {
        try {
            // Skip if pr_no is empty
            if (empty($purchaseOrder->pr_no)) {
                return;
            }

            // Get all purchase orders for the same pr_no
            $allPOsForPR = PurchaseOrder::where('pr_no', $purchaseOrder->pr_no)->get();

            // Check if all POs are approved
            $allApproved = $allPOsForPR->every(function ($po) {
                return $po->status === PurchaseOrder::STATUS_APPROVED;
            });

            Log::info('Checking PR status update:', [
                'pr_no' => $purchaseOrder->pr_no,
                'total_pos' => $allPOsForPR->count(),
                'approved_pos' => $allPOsForPR->where('status', PurchaseOrder::STATUS_APPROVED)->count(),
                'all_approved' => $allApproved
            ]);

            if ($allApproved) {
                // Get individual PurchaseRequest models to trigger Observer events
                $purchaseRequests = PurchaseRequest::where('pr_no', $purchaseOrder->pr_no)->get();
                
                $updated = 0;
                foreach ($purchaseRequests as $pr) {
                    if ($pr->pr_status !== 'approved') {
                        $pr->update(['pr_status' => 'approved']);
                        $updated++;
                    }
                }

                Log::info('PR status updated to approved:', [
                    'pr_no' => $purchaseOrder->pr_no,
                    'updated_count' => $updated
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error checking PR status update:', [
                'pr_no' => $purchaseOrder->pr_no,
                'error' => $e->getMessage()
            ]);
        }
    }
}

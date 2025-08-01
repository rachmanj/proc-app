<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class UpdatePrStatusForApprovedPOs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pr:update-status-for-approved-pos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update pr_status to "approved" for PRs where all related purchase orders are approved';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update PR status for approved POs...');

        $updated = 0;

        // Get all distinct pr_no values from purchase_orders table
        $prNos = PurchaseOrder::whereNotNull('pr_no')
            ->distinct()
            ->pluck('pr_no')
            ->toArray();

        foreach ($prNos as $prNo) {
            // Get all purchase orders for this pr_no
            $allPOsForPR = PurchaseOrder::where('pr_no', $prNo)->get();

            // Check if all POs are approved
            $allApproved = $allPOsForPR->every(function ($po) {
                return $po->status === PurchaseOrder::STATUS_APPROVED;
            });

            $this->line("PR: {$prNo} - Total POs: {$allPOsForPR->count()}, Approved POs: {$allPOsForPR->where('status', PurchaseOrder::STATUS_APPROVED)->count()}, All Approved: " . ($allApproved ? 'Yes' : 'No'));

            if ($allApproved) {
                // Update pr_status to 'approved' for the related purchase request
                $updatedCount = PurchaseRequest::where('pr_no', $prNo)
                    ->where('pr_status', '!=', 'approved')
                    ->update(['pr_status' => 'approved']);

                if ($updatedCount > 0) {
                    $updated += $updatedCount;
                    $this->info("✓ Updated PR: {$prNo} - Status changed to 'approved'");
                } else {
                    $this->line("- PR: {$prNo} - Already approved or not found");
                }
            }
        }

        $this->info("\nUpdate completed!");
        $this->info("Total PRs updated: {$updated}");

        // Display summary
        $totalPRsWithPOs = PurchaseRequest::whereIn('pr_no', $prNos)->count();
        $totalPRsWithApprovedStatus = PurchaseRequest::where('pr_status', 'approved')->count();

        $this->info("\n--- Summary ---");
        $this->info("Total PRs with POs: {$totalPRsWithPOs}");
        $this->info("Total PRs with 'approved' status: {$totalPRsWithApprovedStatus}");

        // Show breakdown by status
        $statusBreakdown = PurchaseRequest::selectRaw('pr_status, count(*) as count')
            ->groupBy('pr_status')
            ->get();

        $this->info("\n--- PR Status Breakdown ---");
        foreach ($statusBreakdown as $status) {
            $this->line("{$status->pr_status}: {$status->count}");
        }

        return Command::SUCCESS;
    }
}

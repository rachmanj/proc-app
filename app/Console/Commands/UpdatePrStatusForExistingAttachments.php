<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseRequest;
use App\Models\PrAttachment;

class UpdatePrStatusForExistingAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pr:update-status-for-attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update pr_status to "progress" for existing PRs that have attachments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update PR status for existing attachments...');

        // Method 1: Update PRs via relationship
        $updatedViaRelationship = 0;
        $purchaseRequests = PurchaseRequest::whereHas('attachments')
            ->where('pr_status', '!=', 'progress')
            ->get();

        foreach ($purchaseRequests as $pr) {
            $pr->update(['pr_status' => 'progress']);
            $updatedViaRelationship++;
            $this->line("Updated PR: {$pr->pr_no} (via relationship)");
        }

        // Method 2: Update PRs via pr_no in attachments table
        $updatedViaPrNo = 0;
        $attachmentPrNos = PrAttachment::whereNotNull('pr_no')
            ->distinct()
            ->pluck('pr_no')
            ->toArray();

        foreach ($attachmentPrNos as $prNo) {
            $updated = PurchaseRequest::where('pr_no', $prNo)
                ->where('pr_status', '!=', 'progress')
                ->update(['pr_status' => 'progress']);
            
            if ($updated > 0) {
                $updatedViaPrNo += $updated;
                $this->line("Updated PR: {$prNo} (via pr_no)");
            }
        }

        $totalUpdated = $updatedViaRelationship + $updatedViaPrNo;

        $this->info("Update completed!");
        $this->info("Total PRs updated: {$totalUpdated}");
        $this->info("- Via relationship: {$updatedViaRelationship}");
        $this->info("- Via pr_no: {$updatedViaPrNo}");

        // Display summary of PRs with attachments
        $totalPrsWithAttachments = PurchaseRequest::whereHas('attachments')->count();
        $totalPrsWithProgressStatus = PurchaseRequest::where('pr_status', 'progress')->count();

        $this->info("\n--- Summary ---");
        $this->info("Total PRs with attachments: {$totalPrsWithAttachments}");
        $this->info("Total PRs with 'progress' status: {$totalPrsWithProgressStatus}");

        return Command::SUCCESS;
    }
}

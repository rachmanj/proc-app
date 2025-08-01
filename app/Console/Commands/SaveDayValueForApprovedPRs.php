<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseRequest;
use Carbon\Carbon;

class SaveDayValueForApprovedPRs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pr:save-day-value-for-approved';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save the final day values for existing approved PRs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to save day values for approved PRs...');

        $updated = 0;

        // Get all approved PRs that don't have a day value yet
        $approvedPRs = PurchaseRequest::where('pr_status', 'approved')
            ->whereNull('day')
            ->get();

        foreach ($approvedPRs as $pr) {
            if ($pr->generated_date) {
                $generatedDate = Carbon::parse($pr->generated_date);
                $today = Carbon::today();
                $dayValue = $generatedDate->diffInDays($today);
                
                // Save the day value
                $pr->update(['day' => $dayValue]);
                $updated++;
                
                $this->line("Updated PR: {$pr->pr_no} - Day value: {$dayValue}");
            } else {
                $this->line("Skipped PR: {$pr->pr_no} - No generated_date");
            }
        }

        $this->info("\nUpdate completed!");
        $this->info("Total approved PRs updated: {$updated}");

        // Display summary
        $totalApprovedPRs = PurchaseRequest::where('pr_status', 'approved')->count();
        $approvedPRsWithDayValue = PurchaseRequest::where('pr_status', 'approved')
            ->whereNotNull('day')
            ->count();

        $this->info("\n--- Summary ---");
        $this->info("Total approved PRs: {$totalApprovedPRs}");
        $this->info("Approved PRs with day value: {$approvedPRsWithDayValue}");

        return Command::SUCCESS;
    }
}

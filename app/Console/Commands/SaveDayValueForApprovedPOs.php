<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseOrder;
use Carbon\Carbon;

class SaveDayValueForApprovedPOs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'po:save-day-value-for-approved';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save the final day values for existing approved Purchase Orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to save day values for approved POs...');

        $updated = 0;

        // Get all approved POs that don't have a day value yet
        $approvedPOs = PurchaseOrder::where('status', 'approved')
            ->whereNull('day')
            ->get();

        $this->info("Found {$approvedPOs->count()} approved POs without day values");

        foreach ($approvedPOs as $po) {
            if ($po->create_date) {
                $createDate = Carbon::parse($po->create_date);
                $today = Carbon::today();
                $dayValue = $createDate->diffInDays($today);
                
                // Save the day value
                $po->update(['day' => $dayValue]);
                $updated++;
                
                $this->line("Updated PO: {$po->doc_num} - Day value: {$dayValue}");
            } else {
                $this->line("Skipped PO: {$po->doc_num} - No create_date");
            }
        }

        $this->info("\nUpdate completed!");
        $this->info("Total approved POs updated: {$updated}");

        // Display summary
        $totalApprovedPOs = PurchaseOrder::where('status', 'approved')->count();
        $approvedPOsWithDayValue = PurchaseOrder::where('status', 'approved')
            ->whereNotNull('day')
            ->count();

        $this->info("\n--- Summary ---");
        $this->info("Total approved POs: {$totalApprovedPOs}");
        $this->info("Approved POs with day value: {$approvedPOsWithDayValue}");
        $this->info("Approved POs without day value: " . ($totalApprovedPOs - $approvedPOsWithDayValue));

        return Command::SUCCESS;
    }
} 
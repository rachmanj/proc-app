<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequestDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDuplicateDetailsCommand extends Command
{
    protected $signature = 'clean:duplicate-details
        {--type=all : Type to clean: all, po, pr}
        {--dry-run : Show what would be deleted without actually deleting}
        {--force : Skip confirmation prompt}';

    protected $description = 'Clean up duplicate records in purchase_order_details and purchase_request_details tables';

    public function handle()
    {
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if (!$force && !$dryRun) {
            if (!$this->confirm('This will delete duplicate records. Continue?', true)) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info('Starting duplicate cleanup...');
        $this->newLine();

        if ($type === 'all' || $type === 'po') {
            $this->cleanPoDetails($dryRun);
        }

        if ($type === 'all' || $type === 'pr') {
            $this->cleanPrDetails($dryRun);
        }

        $this->newLine();
        $this->info('Duplicate cleanup completed!');

        return Command::SUCCESS;
    }

    private function cleanPoDetails($dryRun)
    {
        $this->info('=== Cleaning Purchase Order Details ===');

        // Step 1: Find and remove duplicates FIRST (before generating line_identity)
        $this->info('Identifying duplicates...');

        // Find duplicates by SAP identity (sap_doc_entry + sap_line_num)
        $duplicatesBySap = DB::table('purchase_order_details')
            ->whereNotNull('sap_doc_entry')
            ->whereNotNull('sap_line_num')
            ->select('purchase_order_id', 'sap_doc_entry', 'sap_line_num', DB::raw('COUNT(*) as count'), DB::raw('MIN(id) as keep_id'))
            ->groupBy('purchase_order_id', 'sap_doc_entry', 'sap_line_num')
            ->having('count', '>', 1)
            ->get();

        $deletedBySap = 0;
        foreach ($duplicatesBySap as $duplicate) {
            $toDelete = PurchaseOrderDetail::where('purchase_order_id', $duplicate->purchase_order_id)
                ->where('sap_doc_entry', $duplicate->sap_doc_entry)
                ->where('sap_line_num', $duplicate->sap_line_num)
                ->where('id', '!=', $duplicate->keep_id)
                ->get();

            if ($dryRun) {
                $this->warn("  Would delete {$toDelete->count()} duplicates for PO ID {$duplicate->purchase_order_id}, SAP DocEntry {$duplicate->sap_doc_entry}, LineNum {$duplicate->sap_line_num} (keeping ID {$duplicate->keep_id})");
            } else {
                $ids = $toDelete->pluck('id')->toArray();
                PurchaseOrderDetail::whereIn('id', $ids)->delete();
            }
            $deletedBySap += $toDelete->count();
        }

        // Find duplicates by line_identity
        $duplicatesByIdentity = DB::table('purchase_order_details')
            ->whereNotNull('line_identity')
            ->where(function($query) {
                $query->whereNull('sap_doc_entry')
                      ->orWhereNull('sap_line_num');
            })
            ->select('purchase_order_id', 'line_identity', DB::raw('COUNT(*) as count'), DB::raw('MIN(id) as keep_id'))
            ->groupBy('purchase_order_id', 'line_identity')
            ->having('count', '>', 1)
            ->get();

        $deletedByIdentity = 0;
        foreach ($duplicatesByIdentity as $duplicate) {
            $toDelete = PurchaseOrderDetail::where('purchase_order_id', $duplicate->purchase_order_id)
                ->where('line_identity', $duplicate->line_identity)
                ->where('id', '!=', $duplicate->keep_id)
                ->where(function($query) {
                    $query->whereNull('sap_doc_entry')
                          ->orWhereNull('sap_line_num');
                })
                ->get();

            if ($dryRun) {
                $this->warn("  Would delete {$toDelete->count()} duplicates for PO ID {$duplicate->purchase_order_id}, line_identity {$duplicate->line_identity} (keeping ID {$duplicate->keep_id})");
            } else {
                $ids = $toDelete->pluck('id')->toArray();
                PurchaseOrderDetail::whereIn('id', $ids)->delete();
            }
            $deletedByIdentity += $toDelete->count();
        }

        // Find duplicates by item characteristics (fallback for records without SAP ID or line_identity)
        $duplicatesByItem = DB::table('purchase_order_details as pod1')
            ->leftJoin('purchase_order_details as pod2', function($join) {
                $join->on('pod1.purchase_order_id', '=', 'pod2.purchase_order_id')
                     ->on('pod1.item_code', '=', 'pod2.item_code')
                     ->on('pod1.qty', '=', 'pod2.qty')
                     ->on('pod1.unit_price', '=', 'pod2.unit_price')
                     ->whereRaw('pod1.id < pod2.id');
            })
            ->whereNull('pod1.sap_doc_entry')
            ->whereNull('pod1.sap_line_num')
            ->whereNull('pod1.line_identity')
            ->whereNotNull('pod2.id')
            ->select('pod1.id')
            ->get();

        $deletedByItem = 0;
        if ($duplicatesByItem->isNotEmpty()) {
            $idsToDelete = $duplicatesByItem->pluck('id')->toArray();
            
            if ($dryRun) {
                $this->warn("  Would delete " . count($idsToDelete) . " duplicates by item characteristics");
            } else {
                PurchaseOrderDetail::whereIn('id', $idsToDelete)->delete();
            }
            $deletedByItem = count($idsToDelete);
        }

        // Step 2: For records without line_identity, find duplicates that would have same line_identity
        $this->info('Finding duplicates that would have same line_identity...');
        $detailsWithoutIdentity = PurchaseOrderDetail::whereNull('line_identity')
            ->where(function($query) {
                $query->whereNull('sap_doc_entry')
                      ->orWhereNull('sap_line_num');
            })
            ->get();

        // Group by computed line_identity to find duplicates
        $identityGroups = [];
        foreach ($detailsWithoutIdentity as $detail) {
            $lineIdentity = $this->buildPoLineIdentity($detail->purchase_order_id, $detail);
            if (!isset($identityGroups[$lineIdentity])) {
                $identityGroups[$lineIdentity] = [];
            }
            $identityGroups[$lineIdentity][] = $detail;
        }

        // Delete duplicates that would have same line_identity (keep first one)
        foreach ($identityGroups as $lineIdentity => $group) {
            if (count($group) > 1) {
                // Sort by ID, keep the first (oldest)
                usort($group, function($a, $b) {
                    return $a->id <=> $b->id;
                });
                $keepDetail = array_shift($group);
                
                if ($dryRun) {
                    $this->warn("  Would delete " . count($group) . " duplicates for computed line_identity (keeping ID {$keepDetail->id})");
                } else {
                    $idsToDelete = array_map(function($detail) { return $detail->id; }, $group);
                    PurchaseOrderDetail::whereIn('id', $idsToDelete)->delete();
                    $deletedByIdentity += count($idsToDelete);
                }
            }
        }

        // Step 3: Generate line_identity for remaining records
        $this->info('Generating line_identity for remaining records...');
        $remainingWithoutIdentity = PurchaseOrderDetail::whereNull('line_identity')
            ->where(function($query) {
                $query->whereNull('sap_doc_entry')
                      ->orWhereNull('sap_line_num');
            })
            ->get();

        $updated = 0;
        if (!$dryRun) {
            foreach ($remainingWithoutIdentity as $detail) {
                try {
                    $lineIdentity = $this->buildPoLineIdentity($detail->purchase_order_id, $detail);
                    
                    // Check if this line_identity already exists for this purchase_order_id
                    $existing = PurchaseOrderDetail::where('purchase_order_id', $detail->purchase_order_id)
                        ->where('line_identity', $lineIdentity)
                        ->where('id', '!=', $detail->id)
                        ->first();
                    
                    if ($existing) {
                        // Delete the current record (duplicate), keep the existing one
                        $detail->delete();
                        $deletedByIdentity++;
                        continue;
                    }
                    
                    $detail->line_identity = $lineIdentity;
                    $detail->save();
                    $updated++;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // If unique constraint violation, delete this duplicate
                    $detail->delete();
                    $deletedByIdentity++;
                }
            }
        } else {
            $updated = $remainingWithoutIdentity->count();
        }
        $this->info("→ Updated {$updated} records with line_identity");

        $totalDeleted = $deletedBySap + $deletedByIdentity + $deletedByItem;
        $this->info("→ Found and " . ($dryRun ? 'would delete' : 'deleted') . " {$totalDeleted} duplicate PO detail records");
        $this->info("  - By SAP identity: {$deletedBySap}");
        $this->info("  - By line_identity: {$deletedByIdentity}");
        $this->info("  - By item characteristics: {$deletedByItem}");
        $this->newLine();
    }

    private function cleanPrDetails($dryRun)
    {
        $this->info('=== Cleaning Purchase Request Details ===');

        // Step 1: Find and remove duplicates FIRST (before generating line_identity)
        $this->info('Identifying duplicates...');

        // Find duplicates by SAP identity (sap_doc_entry + sap_line_num)
        $duplicatesBySap = DB::table('purchase_request_details')
            ->whereNotNull('sap_doc_entry')
            ->whereNotNull('sap_line_num')
            ->select('purchase_request_id', 'sap_doc_entry', 'sap_line_num', DB::raw('COUNT(*) as count'), DB::raw('MIN(id) as keep_id'))
            ->groupBy('purchase_request_id', 'sap_doc_entry', 'sap_line_num')
            ->having('count', '>', 1)
            ->get();

        $deletedBySap = 0;
        foreach ($duplicatesBySap as $duplicate) {
            $toDelete = PurchaseRequestDetail::where('purchase_request_id', $duplicate->purchase_request_id)
                ->where('sap_doc_entry', $duplicate->sap_doc_entry)
                ->where('sap_line_num', $duplicate->sap_line_num)
                ->where('id', '!=', $duplicate->keep_id)
                ->get();

            if ($dryRun) {
                $this->warn("  Would delete {$toDelete->count()} duplicates for PR ID {$duplicate->purchase_request_id}, SAP DocEntry {$duplicate->sap_doc_entry}, LineNum {$duplicate->sap_line_num} (keeping ID {$duplicate->keep_id})");
            } else {
                $ids = $toDelete->pluck('id')->toArray();
                PurchaseRequestDetail::whereIn('id', $ids)->delete();
            }
            $deletedBySap += $toDelete->count();
        }

        // Find duplicates by line_identity
        $duplicatesByIdentity = DB::table('purchase_request_details')
            ->whereNotNull('line_identity')
            ->where(function($query) {
                $query->whereNull('sap_doc_entry')
                      ->orWhereNull('sap_line_num');
            })
            ->select('purchase_request_id', 'line_identity', DB::raw('COUNT(*) as count'), DB::raw('MIN(id) as keep_id'))
            ->groupBy('purchase_request_id', 'line_identity')
            ->having('count', '>', 1)
            ->get();

        $deletedByIdentity = 0;
        foreach ($duplicatesByIdentity as $duplicate) {
            $toDelete = PurchaseRequestDetail::where('purchase_request_id', $duplicate->purchase_request_id)
                ->where('line_identity', $duplicate->line_identity)
                ->where('id', '!=', $duplicate->keep_id)
                ->where(function($query) {
                    $query->whereNull('sap_doc_entry')
                          ->orWhereNull('sap_line_num');
                })
                ->get();

            if ($dryRun) {
                $this->warn("  Would delete {$toDelete->count()} duplicates for PR ID {$duplicate->purchase_request_id}, line_identity {$duplicate->line_identity} (keeping ID {$duplicate->keep_id})");
            } else {
                $ids = $toDelete->pluck('id')->toArray();
                PurchaseRequestDetail::whereIn('id', $ids)->delete();
            }
            $deletedByIdentity += $toDelete->count();
        }

        // Find duplicates by item characteristics (fallback for records without SAP ID or line_identity)
        $duplicatesByItem = DB::table('purchase_request_details as prd1')
            ->leftJoin('purchase_request_details as prd2', function($join) {
                $join->on('prd1.purchase_request_id', '=', 'prd2.purchase_request_id')
                     ->on('prd1.item_code', '=', DB::raw('COALESCE(prd2.item_code, "")'))
                     ->on('prd1.item_name', '=', DB::raw('COALESCE(prd2.item_name, "")'))
                     ->on('prd1.quantity', '=', 'prd2.quantity')
                     ->on('prd1.uom', '=', DB::raw('COALESCE(prd2.uom, "")'))
                     ->whereRaw('prd1.id < prd2.id');
            })
            ->whereNull('prd1.sap_doc_entry')
            ->whereNull('prd1.sap_line_num')
            ->whereNull('prd1.line_identity')
            ->whereNotNull('prd2.id')
            ->select('prd1.id')
            ->get();

        $deletedByItem = 0;
        if ($duplicatesByItem->isNotEmpty()) {
            $idsToDelete = $duplicatesByItem->pluck('id')->toArray();
            
            if ($dryRun) {
                $this->warn("  Would delete " . count($idsToDelete) . " duplicates by item characteristics");
            } else {
                PurchaseRequestDetail::whereIn('id', $idsToDelete)->delete();
            }
            $deletedByItem = count($idsToDelete);
        }

        // Step 2: For records without line_identity, find duplicates that would have same line_identity
        $this->info('Finding duplicates that would have same line_identity...');
        $detailsWithoutIdentity = PurchaseRequestDetail::whereNull('line_identity')
            ->where(function($query) {
                $query->whereNull('sap_doc_entry')
                      ->orWhereNull('sap_line_num');
            })
            ->get();

        // Group by computed line_identity to find duplicates
        $identityGroups = [];
        foreach ($detailsWithoutIdentity as $detail) {
            $lineIdentity = $this->buildPrLineIdentity($detail->purchase_request_id, $detail);
            if (!isset($identityGroups[$lineIdentity])) {
                $identityGroups[$lineIdentity] = [];
            }
            $identityGroups[$lineIdentity][] = $detail;
        }

        // Delete duplicates that would have same line_identity (keep first one)
        foreach ($identityGroups as $lineIdentity => $group) {
            if (count($group) > 1) {
                // Sort by ID, keep the first (oldest)
                usort($group, function($a, $b) {
                    return $a->id <=> $b->id;
                });
                $keepDetail = array_shift($group);
                
                if ($dryRun) {
                    $this->warn("  Would delete " . count($group) . " duplicates for computed line_identity (keeping ID {$keepDetail->id})");
                } else {
                    $idsToDelete = array_map(function($detail) { return $detail->id; }, $group);
                    PurchaseRequestDetail::whereIn('id', $idsToDelete)->delete();
                    $deletedByIdentity += count($idsToDelete);
                }
            }
        }

        // Step 3: Generate line_identity for remaining records
        $this->info('Generating line_identity for remaining records...');
        $remainingWithoutIdentity = PurchaseRequestDetail::whereNull('line_identity')
            ->where(function($query) {
                $query->whereNull('sap_doc_entry')
                      ->orWhereNull('sap_line_num');
            })
            ->get();

        $updated = 0;
        if (!$dryRun) {
            foreach ($remainingWithoutIdentity as $detail) {
                try {
                    $lineIdentity = $this->buildPrLineIdentity($detail->purchase_request_id, $detail);
                    
                    // Check if this line_identity already exists for this purchase_request_id
                    $existing = PurchaseRequestDetail::where('purchase_request_id', $detail->purchase_request_id)
                        ->where('line_identity', $lineIdentity)
                        ->where('id', '!=', $detail->id)
                        ->first();
                    
                    if ($existing) {
                        // Delete the current record (duplicate), keep the existing one
                        $detail->delete();
                        $deletedByIdentity++;
                        continue;
                    }
                    
                    $detail->line_identity = $lineIdentity;
                    $detail->save();
                    $updated++;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // If unique constraint violation, delete this duplicate
                    $detail->delete();
                    $deletedByIdentity++;
                }
            }
        } else {
            $updated = $remainingWithoutIdentity->count();
        }
        $this->info("→ Updated {$updated} records with line_identity");

        $totalDeleted = $deletedBySap + $deletedByIdentity + $deletedByItem;
        $this->info("→ Found and " . ($dryRun ? 'would delete' : 'deleted') . " {$totalDeleted} duplicate PR detail records");
        $this->info("  - By SAP identity: {$deletedBySap}");
        $this->info("  - By line_identity: {$deletedByIdentity}");
        $this->info("  - By item characteristics: {$deletedByItem}");
        $this->newLine();
    }

    private function buildPoLineIdentity(int $purchaseOrderId, $detail): string
    {
        // Only use fields that exist in purchase_order_details table
        $data = [
            'po' => $purchaseOrderId,
            'item_code' => $detail->item_code ?? '',
            'description' => $detail->description ?? '',
            'qty' => $detail->qty ?? 0,
            'unit_price' => $detail->unit_price ?? 0,
        ];

        // Normalize null values for consistent hashing
        array_walk($data, function(&$value) {
            $value = $value ?? '';
        });

        return hash('sha1', json_encode($data));
    }

    private function buildPrLineIdentity(int $purchaseRequestId, $detail): string
    {
        $data = [
            'pr' => $purchaseRequestId,
            'item_code' => $detail->item_code ?? '',
            'item_name' => $detail->item_name ?? '',
            'quantity' => $detail->quantity ?? 0,
            'uom' => $detail->uom ?? '',
            'line_remarks' => $detail->line_remarks ?? '',
        ];

        // Normalize null values for consistent hashing
        array_walk($data, function(&$value) {
            $value = $value ?? '';
        });

        return hash('sha1', json_encode($data));
    }
}
<?php

namespace App\Services;

use App\Models\ItemPrice;
use App\Models\ItemPriceHistory;
use App\Models\ItemPriceImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemPriceImportService
{
    /**
     * Process the imported items from a specific batch.
     *
     * @param string $batchId
     * @return array
     */
    public function processBatch($batchId)
    {
        $stats = [
            'total' => 0,
            'processed' => 0,
            'errors' => 0,
        ];

        // Get all pending items from the batch
        $importItems = ItemPriceImport::where('import_batch', $batchId)
            ->where('status', 'pending')
            ->get();

        $stats['total'] = $importItems->count();

        foreach ($importItems as $importItem) {
            DB::beginTransaction();

            try {
                // Ensure start_date is set (use current date if not provided)
                $startDate = $importItem->start_date ?? now()->format('Y-m-d');

                // Create new item price
                $itemPrice = ItemPrice::create([
                    'supplier_id' => $importItem->supplier_id,
                    'item_code' => $importItem->item_code,
                    'item_description' => $importItem->item_description,
                    'part_number' => $importItem->part_number,
                    'brand' => $importItem->brand,
                    'project' => $importItem->project,
                    'warehouse' => $importItem->warehouse,
                    'start_date' => $startDate,
                    'expired_date' => $importItem->expired_date,
                    'uploaded_by' => $importItem->uploaded_by,
                    'uom' => $importItem->uom,
                    'qty' => $importItem->qty,
                    'price' => $importItem->price,
                    'description' => $importItem->description,
                ]);

                // Create history record
                ItemPriceHistory::create([
                    'item_code' => $importItem->item_code,
                    'item_description' => $importItem->item_description,
                    'supplier_id' => $importItem->supplier_id,
                    'project' => $importItem->project,
                    'warehouse' => $importItem->warehouse,
                    'part_number' => $importItem->part_number,
                    'brand' => $importItem->brand,
                    'price' => $importItem->price,
                    'uom' => $importItem->uom,
                    'qty' => $importItem->qty,
                    'start_date' => $startDate,
                    'expired_date' => $importItem->expired_date,
                    'created_by' => $importItem->uploaded_by,
                ]);

                // Update import status
                $importItem->status = 'processed';
                $importItem->save();

                DB::commit();
                $stats['processed']++;
            } catch (\Exception $e) {
                DB::rollBack();

                // Log the error
                Log::error('Error processing import item: ' . $e->getMessage(), [
                    'item_id' => $importItem->id,
                    'batch' => $batchId,
                    'exception' => $e,
                ]);

                // Update import status
                $importItem->status = 'error';
                $importItem->error_message = $e->getMessage();
                $importItem->save();

                $stats['errors']++;
            }
        }

        return $stats;
    }

    /**
     * Get statistics for a batch.
     *
     * @param string $batchId
     * @return array
     */
    public function getBatchStatistics($batchId)
    {
        $total = ItemPriceImport::where('import_batch', $batchId)->count();
        $processed = ItemPriceImport::where('import_batch', $batchId)->where('status', 'processed')->count();
        $errors = ItemPriceImport::where('import_batch', $batchId)->where('status', 'error')->count();
        $pending = ItemPriceImport::where('import_batch', $batchId)->where('status', 'pending')->count();

        $errorItems = ItemPriceImport::where('import_batch', $batchId)
            ->where('status', 'error')
            ->get();

        return [
            'total' => $total,
            'processed' => $processed,
            'errors' => $errors,
            'pending' => $pending,
            'items' => $errorItems,
        ];
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PrTemp;
use App\Models\PoTemp;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use App\Models\SyncLog;
use App\Services\SapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncWithSapController extends Controller
{
    public function index()
    {
        $lastPrSync = SyncLog::where('data_type', 'PR')
            ->orderBy('created_at', 'desc')
            ->first();
        
        $lastPoSync = SyncLog::where('data_type', 'PO')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('master.sync-with-sap.index', compact('lastPrSync', 'lastPoSync'));
    }

    public function syncPr(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $syncLog = SyncLog::create([
            'data_type' => 'PR',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'user_id' => auth()->id(),
            'sync_status' => 'success',
        ]);

        try {
            Log::info('Starting PR sync from SAP', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $sapService = new SapService();
            
            // Execute SQL query
            $results = $sapService->executePrSqlQuery($startDate, $endDate);

            if (empty($results)) {
                $syncLog->update([
                    'sync_status' => 'failed',
                    'error_message' => 'No data found for the selected date range',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the selected date range',
                    'sync_log' => $syncLog,
                ]);
            }

            // Clear existing data
            PrTemp::truncate();
            Log::info('Cleared existing temporary PR data');

            // Map and insert data
            $insertData = [];
            foreach ($results as $row) {
                $insertData[] = $sapService->mapPrResultToModel($row);
            }

            // Bulk insert in chunks for better performance
            $chunks = array_chunk($insertData, 500);
            foreach ($chunks as $chunk) {
                PrTemp::insert($chunk);
            }

            $recordsSynced = PrTemp::count();
            $syncLog->update(['records_synced' => $recordsSynced]);

            Log::info('PR sync completed successfully', [
                'row_count' => $recordsSynced,
            ]);

            // Auto-convert to PR table
            $convertResult = $this->convertPrToTable();
            
            $syncLog->update([
                'records_created' => $convertResult['imported'],
                'records_skipped' => $convertResult['skipped'],
                'convert_status' => $convertResult['status'],
                'error_message' => $convertResult['error'] ?? null,
            ]);

            $message = "Successfully synced {$recordsSynced} PR records. ";
            $message .= "Created: {$convertResult['imported']}, Skipped: {$convertResult['skipped']}";

            if ($convertResult['status'] === 'failed') {
                $message .= ". Conversion failed: " . ($convertResult['error'] ?? 'Unknown error');
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'sync_log' => $syncLog->fresh(),
            ]);

        } catch (\Exception $e) {
            Log::error('PR Sync Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $syncLog->update([
                'sync_status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error syncing data from SAP: ' . $e->getMessage(),
                'sync_log' => $syncLog->fresh(),
            ], 500);
        }
    }

    public function syncPo(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $syncLog = SyncLog::create([
            'data_type' => 'PO',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'user_id' => auth()->id(),
            'sync_status' => 'success',
        ]);

        try {
            Log::info('Starting PO sync from SAP', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $sapService = new SapService();
            
            // Execute SQL query
            $results = $sapService->executePoSqlQuery($startDate, $endDate);

            if (empty($results)) {
                $syncLog->update([
                    'sync_status' => 'failed',
                    'error_message' => 'No data found for the selected date range',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the selected date range',
                    'sync_log' => $syncLog,
                ]);
            }

            // Clear existing data
            PoTemp::truncate();
            Log::info('Cleared existing temporary PO data');

            // Map and insert data
            $insertData = [];
            foreach ($results as $row) {
                $insertData[] = $sapService->mapPoResultToModel($row);
            }

            // Bulk insert in chunks for better performance
            $chunks = array_chunk($insertData, 500);
            foreach ($chunks as $chunk) {
                PoTemp::insert($chunk);
            }

            $recordsSynced = PoTemp::count();
            $syncLog->update(['records_synced' => $recordsSynced]);

            Log::info('PO sync completed successfully', [
                'row_count' => $recordsSynced,
            ]);

            // Auto-convert to PO table
            $convertResult = $this->convertPoToTable();
            
            $syncLog->update([
                'records_created' => $convertResult['imported'],
                'records_skipped' => $convertResult['skipped'],
                'convert_status' => $convertResult['status'],
                'error_message' => $convertResult['error'] ?? null,
            ]);

            $message = "Successfully synced {$recordsSynced} PO records. ";
            $message .= "Created: {$convertResult['imported']}, Skipped: {$convertResult['skipped']}";

            if ($convertResult['status'] === 'failed') {
                $message .= ". Conversion failed: " . ($convertResult['error'] ?? 'Unknown error');
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'sync_log' => $syncLog->fresh(),
            ]);

        } catch (\Exception $e) {
            Log::error('PO Sync Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $syncLog->update([
                'sync_status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error syncing data from SAP: ' . $e->getMessage(),
                'sync_log' => $syncLog->fresh(),
            ], 500);
        }
    }

    private function convertPrToTable()
    {
        try {
            $prGroups = PrTemp::select(
                'pr_no',
                'pr_draft_no',
                'pr_date',
                'project_code',
                'dept_name',
                'priority',
                'pr_status',
                'pr_type',
                'requestor',
                'for_unit',
                'hours_meter',
                'required_date',
                'remarks',
                'pr_rev_no'
            )
                ->distinct()
                ->get();

            if ($prGroups->isEmpty()) {
                return [
                    'status' => 'skipped',
                    'imported' => 0,
                    'skipped' => 0,
                    'error' => 'No data to import',
                ];
            }

            $importedCount = 0;
            $skippedCount = 0;

            foreach ($prGroups as $prGroup) {
                $existingPR = PurchaseRequest::where('pr_no', $prGroup->pr_no)->first();
                
                if ($existingPR) {
                    $skippedCount++;
                    continue;
                }

                $purchaseRequest = PurchaseRequest::create([
                    'pr_draft_no' => $prGroup->pr_draft_no,
                    'pr_no' => $prGroup->pr_no,
                    'pr_date' => $prGroup->pr_date,
                    'generated_date' => now(),
                    'priority' => $prGroup->priority ?? 'NORMAL',
                    'pr_status' => $prGroup->pr_status ?? 'OPEN',
                    'closed_status' => 'OPEN',
                    'pr_rev_no' => $prGroup->pr_rev_no,
                    'pr_type' => $prGroup->pr_type,
                    'project_code' => $prGroup->project_code,
                    'dept_name' => $prGroup->dept_name,
                    'for_unit' => $prGroup->for_unit,
                    'hours_meter' => $prGroup->hours_meter,
                    'required_date' => $prGroup->required_date,
                    'requestor' => $prGroup->requestor,
                    'remarks' => $prGroup->remarks
                ]);

                $prDetails = PrTemp::where('pr_no', $prGroup->pr_no)
                    ->select([
                        'item_code',
                        'item_name',
                        'quantity',
                        'uom',
                        'line_remarks',
                        'sap_doc_entry',
                        'sap_line_num',
                        'sap_vis_order',
                    ])
                    ->get();

                foreach ($prDetails as $detail) {
                    $this->upsertPurchaseRequestDetail($purchaseRequest->id, $detail);
                }

                $importedCount++;
            }

            PrTemp::truncate();

            return [
                'status' => 'success',
                'imported' => $importedCount,
                'skipped' => $skippedCount,
            ];

        } catch (\Exception $e) {
            Log::error('PR Convert Error: ' . $e->getMessage());
            return [
                'status' => 'failed',
                'imported' => 0,
                'skipped' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function convertPoToTable()
    {
        try {
            $poGroups = PoTemp::select(
                'po_no',
                'posting_date',
                'create_date',
                'po_delivery_date',
                'po_eta',
                'pr_no',
                'unit_no',
                'vendor_code',
                'vendor_name',
                'po_currency',
                'total_po_price',
                'po_with_vat',
                'project_code',
                'dept_code',
                'po_status',
                'po_delivery_status',
                'budget_type'
            )
                ->distinct('po_no')
                ->get();

            if ($poGroups->isEmpty()) {
                return [
                    'status' => 'skipped',
                    'imported' => 0,
                    'skipped' => 0,
                    'error' => 'No data to copy',
                ];
            }

            $importedCount = 0;
            $skippedCount = 0;
            $createdSuppliers = 0;

            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($poGroups as $poGroup) {
                $existingPO = PurchaseOrder::where('doc_num', $poGroup->po_no)->first();
                
                if ($existingPO) {
                    $skippedCount++;
                    continue;
                }

                $supplier = Supplier::firstOrCreate(
                    ['code' => $poGroup->vendor_code],
                    [
                        'name' => $poGroup->vendor_name,
                        'type' => 'vendor',
                        'project' => $poGroup->project_code
                    ]
                );

                if ($supplier->wasRecentlyCreated) {
                    $createdSuppliers++;
                }

                $purchaseOrder = PurchaseOrder::create([
                    'doc_num' => $poGroup->po_no,
                    'doc_date' => $poGroup->posting_date,
                    'create_date' => $poGroup->create_date,
                    'po_delivery_date' => $poGroup->po_delivery_date,
                    'pr_no' => $poGroup->pr_no,
                    'unit_no' => $poGroup->unit_no,
                    'po_eta' => $poGroup->po_eta,
                    'supplier_id' => $supplier->id,
                    'po_currency' => $poGroup->po_currency,
                    'total_po_price' => $poGroup->total_po_price,
                    'po_with_vat' => $poGroup->po_with_vat,
                    'project_code' => $poGroup->project_code,
                    'dept_code' => $poGroup->dept_code,
                    'po_status' => $poGroup->po_status ?? 'OPEN',
                    'po_delivery_status' => $poGroup->po_delivery_status ?? 'OPEN',
                    'budget_type' => $poGroup->budget_type
                ]);

                $poDetails = PoTemp::where('po_no', $poGroup->po_no)
                    ->select([
                        'item_code',
                        'description',
                        'remark1',
                        'remark2',
                        'qty',
                        'uom',
                        'unit_price',
                        'item_amount',
                        'sap_doc_entry',
                        'sap_line_num',
                        'sap_vis_order',
                    ])
                    ->get();

                foreach ($poDetails as $detail) {
                    $this->upsertPurchaseOrderDetail($purchaseOrder->id, $detail);
                }

                $importedCount++;
            }

            PoTemp::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return [
                'status' => 'success',
                'imported' => $importedCount,
                'skipped' => $skippedCount,
            ];

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            Log::error('PO Convert Error: ' . $e->getMessage());
            return [
                'status' => 'failed',
                'imported' => 0,
                'skipped' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function truncatePrTemp()
    {
        try {
            PrTemp::truncate();
            return response()->json([
                'success' => true,
                'message' => 'PR temporary table cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing PR temporary table: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function truncatePoTemp()
    {
        try {
            PoTemp::truncate();
            return response()->json([
                'success' => true,
                'message' => 'PO temporary table cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing PO temporary table: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function upsertPurchaseOrderDetail(int $purchaseOrderId, $detail): void
    {
        $lineIdentity = $this->buildLineIdentity($purchaseOrderId, $detail);

        $uniqueKeys = [
            'purchase_order_id' => $purchaseOrderId,
        ];

        if (!is_null($detail->sap_doc_entry) && !is_null($detail->sap_line_num)) {
            $uniqueKeys['sap_doc_entry'] = $detail->sap_doc_entry;
            $uniqueKeys['sap_line_num'] = $detail->sap_line_num;
        } else {
            $uniqueKeys['line_identity'] = $lineIdentity;
        }

        PurchaseOrderDetail::updateOrCreate($uniqueKeys, [
            'item_code' => $detail->item_code,
            'description' => $detail->description,
            'remark1' => $detail->remark1,
            'remark2' => $detail->remark2,
            'qty' => $detail->qty,
            'uom' => $detail->uom,
            'unit_price' => $detail->unit_price,
            'item_amount' => $detail->item_amount,
            'sap_doc_entry' => $detail->sap_doc_entry,
            'sap_line_num' => $detail->sap_line_num,
            'sap_vis_order' => $detail->sap_vis_order,
            'line_identity' => $lineIdentity,
        ]);
    }

    private function buildLineIdentity(int $purchaseOrderId, $detail): string
    {
        return hash('sha1', json_encode([
            'po' => $purchaseOrderId,
            'item_code' => $detail->item_code,
            'description' => $detail->description,
            'remark1' => $detail->remark1,
            'remark2' => $detail->remark2,
            'qty' => $detail->qty,
            'unit_price' => $detail->unit_price,
        ]));
    }

    private function upsertPurchaseRequestDetail(int $purchaseRequestId, $detail): void
    {
        $quantity = is_numeric($detail->quantity) ? (float)$detail->quantity : 0;
        $lineIdentity = $this->buildPrLineIdentity($purchaseRequestId, $detail);

        $payload = [
            'purchase_request_id' => $purchaseRequestId,
            'item_code' => $detail->item_code,
            'item_name' => $detail->item_name,
            'quantity' => $quantity,
            'uom' => $detail->uom,
            'open_qty' => $quantity,
            'line_remarks' => $detail->line_remarks ?? '',
            'status' => 'OPEN',
            'sap_doc_entry' => $detail->sap_doc_entry,
            'sap_line_num' => $detail->sap_line_num,
            'sap_vis_order' => $detail->sap_vis_order,
            'line_identity' => $lineIdentity,
        ];

        if (!is_null($detail->sap_doc_entry) && !is_null($detail->sap_line_num)) {
            $existing = PurchaseRequestDetail::where('purchase_request_id', $purchaseRequestId)
                ->where('line_identity', $lineIdentity)
                ->first();

            if ($existing) {
                $existing->fill($payload)->save();
                return;
            }

            PurchaseRequestDetail::updateOrCreate([
                'purchase_request_id' => $purchaseRequestId,
                'sap_doc_entry' => $detail->sap_doc_entry,
                'sap_line_num' => $detail->sap_line_num,
            ], $payload);

            return;
        }

        PurchaseRequestDetail::updateOrCreate([
            'purchase_request_id' => $purchaseRequestId,
            'line_identity' => $lineIdentity,
        ], $payload);
    }

    private function buildPrLineIdentity(int $purchaseRequestId, $detail): string
    {
        return hash('sha1', json_encode([
            'pr' => $purchaseRequestId,
            'item_code' => $detail->item_code,
            'item_name' => $detail->item_name,
            'quantity' => $detail->quantity,
            'uom' => $detail->uom,
            'line_remarks' => $detail->line_remarks ?? '',
        ]));
    }
}

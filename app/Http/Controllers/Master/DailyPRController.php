<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Imports\PRTempImport;
use App\Models\PrTemp;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use App\Services\SapService;

class DailyPRController extends Controller
{
    public function index()
    {
        $page = request()->query('page', 'dashboard');

        $views = [
            'dashboard' => 'master.dailypr.dashboard',
            'search' => 'master.dailypr.search',
            'create' => 'master.dailypr.create',
            'list' => 'master.dailypr.list',
        ];

        return view($views[$page]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            // Clear existing temporary data
            PrTemp::truncate();

            // Import new data
            $import = new PRTempImport;
            Excel::import($import, $request->file('file'));

            $rowCount = PrTemp::count(); // Get count of imported rows

            return redirect()->back()->with('success', "Successfully imported {$rowCount} rows of data.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    public function syncFromSap(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Validate date range (max 90 days)
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $diff = $start->diff($end);
            
            if ($diff->days > 90) {
                return redirect()->back()->with('error', 'Date range cannot exceed 90 days');
            }

            Log::info('Starting PR sync from SAP', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $sapService = new SapService();
            
            // Execute SQL query
            $results = $sapService->executePrSqlQuery($startDate, $endDate);

            if (empty($results)) {
                return redirect()->back()->with('info', 'No data found for the selected date range');
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

            $rowCount = PrTemp::count();
            Log::info('PR sync completed successfully', [
                'row_count' => $rowCount,
            ]);

            return redirect()->back()->with('success', "Successfully synced {$rowCount} records from SAP");
        } catch (\Exception $e) {
            Log::error('PR Sync Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Error syncing data from SAP: ' . $e->getMessage());
        }
    }

    public function data()
    {
        $query = PrTemp::select([
            'pr_no',
            'pr_date',
            'project_code',
            'dept_name',
            'item_name',
            'quantity',
            'uom',
            'pr_status'
        ]);

        return DataTables::of($query)->toJson();
    }

    public function importToPRTable()
    {
        try {
            // Get all unique PR temps
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
                throw new \Exception('No data to import');
            }

            $importedCount = 0;
            $skippedCount = 0;
            $currentPrNo = null;

            foreach ($prGroups as $prGroup) {
                $currentPrNo = $prGroup->pr_no;

                // Check if PR number already exists in purchase_requests table
                $existingPR = PurchaseRequest::where('pr_no', $prGroup->pr_no)->first();
                
                if ($existingPR) {
                    // Skip this PR as it already exists
                    $skippedCount++;
                    Log::info("Skipped PR: {$prGroup->pr_no} - already exists");
                    continue;
                }

                // Create Purchase Request
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

                // Get details for this PR
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

            // Clear temporary data only if we have successfully imported
            if ($importedCount > 0 || $skippedCount > 0) {
                PrTemp::truncate();
            }

            // Prepare success message
            $message = "Import completed: {$importedCount} Purchase Requests imported";
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} skipped (duplicate PR numbers)";
            }

            // Set flash message in session
            session()->flash('success', $message);

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $importedCount,
                'skipped' => $skippedCount,
                'reload_page' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Set flash message in session
            session()->flash('error', 'Error importing data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage(),
                'reload_page' => true
            ], 500);
        }
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

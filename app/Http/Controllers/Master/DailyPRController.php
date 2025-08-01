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
                        'line_remarks'
                    ])
                    ->get();

                foreach ($prDetails as $detail) {
                    $quantity = is_numeric($detail->quantity) ? (float)$detail->quantity : 0;

                    PurchaseRequestDetail::create([
                        'purchase_request_id' => $purchaseRequest->id,
                        'item_code' => $detail->item_code,
                        'item_name' => $detail->item_name,
                        'quantity' => $quantity,
                        'uom' => $detail->uom,
                        'open_qty' => $quantity,
                        'line_remarks' => $detail->line_remarks ?? '',
                        'status' => 'OPEN'
                    ]);
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
}

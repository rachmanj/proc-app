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

            return response()->json([
                'success' => true,
                'message' => 'Data imported successfully',
                'rowCount' => $rowCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
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
        $connection = DB::connection();

        try {
            // Disable foreign key checks
            $connection->statement('SET FOREIGN_KEY_CHECKS=0');

            // Start a new transaction
            $connection->beginTransaction();

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
            $currentPrNo = null;

            foreach ($prGroups as $prGroup) {
                try {
                    $currentPrNo = $prGroup->pr_no;

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
                } catch (\Exception $e) {
                    Log::error("Error processing PR {$currentPrNo}: " . $e->getMessage());
                    throw $e;
                }
            }

            if ($importedCount > 0) {
                PrTemp::truncate();
            }

            // Commit the transaction
            $connection->commit();

            // Re-enable foreign key checks
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} Purchase Requests",
                'count' => $importedCount
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction if it's still active
            if ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }

            // Re-enable foreign key checks
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');

            Log::error('Import Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
        }
    }
}

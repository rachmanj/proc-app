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
            'Quantity',
            'uom',
            'pr_status'
        ]);

        return DataTables::of($query)->toJson();
    }

    public function importToPRTable()
    {
        DB::beginTransaction();

        try {
            // Get all PR temps grouped by PR number
            $prGroups = PrTemp::select(
                'pr_no',
                'pr_date',
                'project_code',
                'dept_name',
                'priority',
                'pr_status',
                'pr_type',
                'requestor'
            )
                ->groupBy(
                    'pr_no',
                    'pr_date',
                    'project_code',
                    'dept_name',
                    'priority',
                    'pr_status',
                    'pr_type',
                    'requestor'
                )
                ->get();

            $importedCount = 0;

            foreach ($prGroups as $prGroup) {
                // Create Purchase Request
                $purchaseRequest = PurchaseRequest::create([
                    'pr_no' => $prGroup->pr_no,
                    'pr_date' => $prGroup->pr_date,
                    'generated_date' => now(),
                    'priority' => $prGroup->priority,
                    'pr_status' => $prGroup->pr_status,
                    'closed_status' => 'OPEN',
                    'pr_type' => $prGroup->pr_type,
                    'project_code' => $prGroup->project_code,
                    'dept_name' => $prGroup->dept_name,
                    'requestor' => $prGroup->requestor
                ]);

                // Get details for this PR
                $prDetails = PrTemp::where('pr_no', $prGroup->pr_no)->get();

                // Create Purchase Request Details
                foreach ($prDetails as $detail) {
                    PurchaseRequestDetail::create([
                        'purchase_request_id' => $purchaseRequest->id,
                        'item_code' => $detail->item_code,
                        'item_name' => $detail->item_name,
                        'quantity' => $detail->Quantity,
                        'uom' => $detail->uom,
                        'open_qty' => $detail->Quantity, // Initially, open qty equals the original quantity
                        'status' => 'OPEN'
                    ]);
                }

                $importedCount++;
            }

            // Clear the temporary table
            PrTemp::truncate();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} Purchase Requests",
                'count' => $importedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
        }
    }
}

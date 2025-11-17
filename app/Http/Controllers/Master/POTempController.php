<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoTemp;
use Yajra\DataTables\Facades\DataTables;
use App\Imports\POTempImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Support\Facades\Log;
use App\Models\Supplier;
use App\Services\SapService;


class POTempController extends Controller
{
    public function index()
    {
        $page = request()->query('page', 'dashboard');
        
        $views = [
            'dashboard' => 'master.po-temp.dashboard',
            'list' => 'master.po-temp.list',
        ];

        return view($views[$page]);
    }

    public function data()
    {
            $query = PoTemp::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('posting_date', function($row) {
                return $row->posting_date ? date('Y-m-d', strtotime($row->posting_date)) : '';
            })
            ->editColumn('create_date', function($row) {
                return $row->create_date ? date('Y-m-d', strtotime($row->create_date)) : '';
            })
            ->editColumn('po_delivery_date', function($row) {
                return $row->po_delivery_date ? date('Y-m-d', strtotime($row->po_delivery_date)) : '';
            })
            ->editColumn('po_eta', function($row) {
                return $row->po_eta ? date('Y-m-d', strtotime($row->po_eta)) : '';
            })
            ->editColumn('unit_price', function($row) {
                return number_format($row->unit_price, 2);
            })
            ->editColumn('item_amount', function($row) {
                return number_format($row->item_amount, 2);
            })
            ->editColumn('total_po_price', function($row) {
                return number_format($row->total_po_price, 2);
            })
            ->editColumn('po_with_vat', function($row) {
                return number_format($row->po_with_vat, 2);
            })
            ->make(true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Log::info('Starting import process');
            Log::info('File details:', [
                'name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'mime' => $request->file('file')->getMimeType()
            ]);

            // Clear existing data
            PoTemp::truncate();
            Log::info('Cleared existing temporary data');

            // Import new data
            Excel::import(new POTempImport, $request->file('file'));
            Log::info('Excel import completed');
            
            return response()->json([
                'success' => true,
                'message' => 'Data imported successfully',
                'rowCount' => PoTemp::count()
            ]);
        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Date range cannot exceed 90 days'
                ], 400);
            }

            Log::info('Starting PO sync from SAP', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $sapService = new SapService();
            
            // Execute SQL query
            $results = $sapService->executePoSqlQuery($startDate, $endDate);

            if (empty($results)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No data found for the selected date range',
                    'rowCount' => 0
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

            $rowCount = PoTemp::count();
            Log::info('PO sync completed successfully', [
                'row_count' => $rowCount,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$rowCount} records from SAP",
                'rowCount' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('PO Sync Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error syncing data from SAP: ' . $e->getMessage()
            ], 500);
        }
    }

    public function copyToPO()
    {
        try {
            // Get all unique PO temps grouped by PO number
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
                throw new \Exception('No data to copy');
            }

            $importedCount = 0;
            $skippedCount = 0;
            $createdSuppliers = 0;

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($poGroups as $poGroup) {
                // Check if PO doc_num already exists in purchase_orders table
                $existingPO = PurchaseOrder::where('doc_num', $poGroup->po_no)->first();
                
                if ($existingPO) {
                    // Skip this PO as it already exists
                    $skippedCount++;
                    Log::info("Skipped PO: {$poGroup->po_no} - already exists");
                    continue;
                }

                // Find or create supplier
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

                // Create Purchase Order
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

                // Get and create details for this PO
                $poDetails = PoTemp::where('po_no', $poGroup->po_no)
                    ->select([
                        'item_code',
                        'description',
                        'remark1',
                        'remark2',
                        'qty',
                        'uom',
                        'unit_price',
                        'item_amount'
                    ])
                    ->get();

                foreach ($poDetails as $detail) {
                    PurchaseOrderDetail::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'item_code' => $detail->item_code,
                        'description' => $detail->description,
                        'remark1' => $detail->remark1,
                        'remark2' => $detail->remark2,
                        'qty' => $detail->qty,
                        'uom' => $detail->uom,
                        'unit_price' => $detail->unit_price,
                        'item_amount' => $detail->item_amount,
                    ]);
                }

                $importedCount++;
            }

            // Clear temporary data only if we have successfully imported or skipped
            if ($importedCount > 0 || $skippedCount > 0) {
                PoTemp::truncate();
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Prepare success message
            $message = "Copy completed: {$importedCount} Purchase Orders copied";
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} skipped (duplicate doc_num)";
            }
            if ($createdSuppliers > 0) {
                $message .= ", {$createdSuppliers} new suppliers created";
            }

            // Set flash message in session
            session()->flash('success', $message);

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $importedCount,
                'skipped' => $skippedCount,
                'suppliers_created' => $createdSuppliers,
                'reload_page' => true
            ]);

        } catch (\Exception $e) {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            Log::error('Copy Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Set flash message in session
            session()->flash('error', 'Error copying data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error copying data: ' . $e->getMessage(),
                'reload_page' => true
            ], 500);
        }
    }
}

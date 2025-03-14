<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POTemp;
use Yajra\DataTables\Facades\DataTables;
use App\Imports\POTempImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Support\Facades\Log;
use App\Models\Supplier;

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
        $query = POTemp::query();

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

        DB::beginTransaction();
        try {
            Excel::import(new POTempImport, $request->file('file'));
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data imported successfully',
                'rowCount' => POTemp::count() // Returns total count after import
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function copyToPO()
    {
        try {
            // Get all unique PO temps grouped by PO number
            $poGroups = POTemp::select(
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
            $createdSuppliers = 0;

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($poGroups as $poGroup) {
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
                $poDetails = POTemp::where('po_no', $poGroup->po_no)
                    ->select([
                        'item_code',
                        'description',
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
                        'qty' => $detail->qty,
                        'uom' => $detail->uom,
                        'unit_price' => $detail->unit_price,
                        'item_amount' => $detail->item_amount,
                    ]);
                }

                $importedCount++;
            }

            if ($importedCount > 0) {
                POTemp::truncate();
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return response()->json([
                'success' => true,
                'message' => "Successfully copied {$importedCount} Purchase Orders" . 
                            ($createdSuppliers > 0 ? " and created {$createdSuppliers} new suppliers" : ""),
                'count' => $importedCount,
                'suppliers_created' => $createdSuppliers
            ]);

        } catch (\Exception $e) {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            Log::error('Copy Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error copying data: ' . $e->getMessage()
            ], 500);
        }
    }
}

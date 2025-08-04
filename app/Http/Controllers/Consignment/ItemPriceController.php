<?php

namespace App\Http\Controllers\Consignment;

use App\Http\Controllers\Controller;
use App\Models\ItemPrice;
use App\Models\ItemPriceHistory;
use App\Models\ItemPriceImport;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItemPriceController extends Controller
{
    public function __construct()
    {
        // Permission is handled in the routes file
    }

    /**
     * Display a listing of the item prices.
     */
    public function index(Request $request)
    {
        $query = ItemPrice::with(['supplier', 'uploader']);

        // Apply filters if provided
        if ($request->filled('item_code')) {
            $query->where('item_code', 'like', '%' . $request->item_code . '%');
        }

        if ($request->filled('item_description')) {
            $query->where('item_description', 'like', '%' . $request->item_description . '%');
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('project')) {
            $query->where('project', 'like', '%' . $request->project . '%');
        }

        if ($request->filled('warehouse')) {
            $query->where('warehouse', 'like', '%' . $request->warehouse . '%');
        }

        $itemPrices = $query->orderBy('created_at', 'desc')->paginate(10);
        $suppliers = Supplier::orderBy('name')->get();
        $projects = Project::orderBy('code')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('consignment.item-prices.index', compact('itemPrices', 'suppliers', 'projects', 'warehouses'));
    }

    /**
     * Show the form for creating a new item price.
     */
    public function create()
    {
        // Permission is handled in the routes file

        $suppliers = Supplier::orderBy('name')->get();
        $projects = Project::orderBy('code')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('consignment.item-prices.create', compact('suppliers', 'projects', 'warehouses'));
    }

    /**
     * Store a newly created item price in storage.
     */
    public function store(Request $request)
    {
        // Permission is handled in the routes file

        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'item_code' => 'required|string|max:255',
            'item_description' => 'required|string|max:255',
            'part_number' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'project' => 'required|string|max:255',
            'warehouse' => 'required|string|max:255',
            'start_date' => 'required|date',
            'expired_date' => 'nullable|date|after_or_equal:start_date',
            'uom' => 'required|string|max:255',
            'qty' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Create new item price
            $itemPrice = ItemPrice::create([
                'supplier_id' => $request->supplier_id,
                'item_code' => $request->item_code,
                'item_description' => $request->item_description,
                'part_number' => $request->part_number,
                'brand' => $request->brand,
                'project' => $request->project,
                'warehouse' => $request->warehouse,
                'start_date' => $request->start_date,
                'expired_date' => $request->expired_date,
                'uploaded_by' => Auth::id(),
                'uom' => $request->uom,
                'qty' => $request->qty,
                'price' => $request->price,
                'description' => $request->description,
            ]);

            // Create history record
            ItemPriceHistory::create([
                'item_code' => $request->item_code,
                'item_description' => $request->item_description,
                'supplier_id' => $request->supplier_id,
                'project' => $request->project,
                'warehouse' => $request->warehouse,
                'part_number' => $request->part_number,
                'brand' => $request->brand,
                'price' => $request->price,
                'uom' => $request->uom,
                'qty' => $request->qty,
                'start_date' => $request->start_date,
                'expired_date' => $request->expired_date,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('consignment.item-prices.index')
                ->with('success', 'Item price created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while creating the item price: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified item price.
     */
    public function show($id)
    {
        $itemPrice = ItemPrice::with(['supplier', 'uploader'])->findOrFail($id);

        // Get price history for this item code
        $priceHistory = ItemPriceHistory::where('item_code', $itemPrice->item_code)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('consignment.item-prices.show', compact('itemPrice', 'priceHistory'));
    }

    /**
     * Remove the specified item price from storage.
     */
    public function destroy($id)
    {
        // Permission is handled in the routes file

        $itemPrice = ItemPrice::findOrFail($id);

        try {
            $itemPrice->delete();
            return redirect()->route('consignment.item-prices.index')
                ->with('success', 'Item price deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the item price: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for uploading item prices from Excel.
     */
    public function showUploadForm()
    {
        // Permission is handled in the routes file

        return view('consignment.item-prices.upload');
    }

    /**
     * Process the uploaded Excel file.
     */
    public function processUpload(Request $request)
    {
        // Permission is handled in the routes file

        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process will be implemented with Excel import service
        // This is a placeholder for now
        return redirect()->route('consignment.item-prices.index')
            ->with('success', 'File uploaded successfully. Processing will be implemented in the next phase.');
    }

    /**
     * Download Excel template for item price upload.
     */
    public function downloadTemplate()
    {
        // This will be implemented with Excel export service
        // For now, just a placeholder
        return redirect()->back()
            ->with('info', 'Template download will be implemented in the next phase.');
    }

    /**
     * Search for item prices.
     */
    public function search(Request $request)
    {
        // Permission is handled in the routes file

        $query = ItemPrice::with(['supplier', 'uploader']);

        // Apply filters if provided
        if ($request->filled('item_code')) {
            $query->where('item_code', 'like', '%' . $request->item_code . '%');
        }

        if ($request->filled('item_description')) {
            $query->where('item_description', 'like', '%' . $request->item_description . '%');
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('project')) {
            $query->where('project', $request->project);
        }

        if ($request->filled('warehouse')) {
            $query->where('warehouse', $request->warehouse);
        }

        if ($request->filled('part_number')) {
            $query->where('part_number', 'like', '%' . $request->part_number . '%');
        }

        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }

        // Get all results for DataTables instead of paginating
        $itemPrices = $query->orderBy('item_code', 'asc')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $projects = Project::orderBy('code')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('consignment.item-prices.search', compact('itemPrices', 'suppliers', 'projects', 'warehouses'));
    }

    /**
     * View price history for a specific item.
     */
    public function history($itemCode)
    {
        // Permission is handled in the routes file

        $priceHistory = ItemPriceHistory::with(['supplier', 'creator'])
            ->where('item_code', $itemCode)
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        $itemInfo = ItemPrice::where('item_code', $itemCode)->first();

        return view('consignment.item-prices.history', compact('priceHistory', 'itemInfo', 'itemCode'));
    }
}

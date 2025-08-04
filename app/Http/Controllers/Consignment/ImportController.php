<?php

namespace App\Http\Controllers\Consignment;

use App\Exports\ItemPricesTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\ItemPricesImport;
use App\Models\ItemPrice;
use App\Models\ItemPriceHistory;
use App\Models\ItemPriceImport;
use App\Models\Supplier;
use App\Services\ItemPriceImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function __construct()
    {
        // Permission is handled in the routes file
    }

    /**
     * Show the upload form.
     */
    public function showUploadForm()
    {
        return view('consignment.imports.upload');
    }

    /**
     * Process the uploaded Excel file.
     */
    public function processUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'project' => 'nullable|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'start_date' => 'nullable|date',
            'expired_date' => 'nullable|date|after_or_equal:start_date',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate a unique batch ID for this import
        $importBatch = 'import_' . Str::random(10) . '_' . time();

        // Get warehouse name if warehouse_id is provided
        $warehouseName = null;
        if ($request->filled('warehouse_id')) {
            $warehouse = \App\Models\Warehouse::find($request->warehouse_id);
            if ($warehouse) {
                $warehouseName = $warehouse->name;
            }
        }

        // Prepare import options
        $importOptions = [
            'batch_id' => $importBatch,
            'supplier_id' => $request->supplier_id,
            'project' => $request->project,
            'warehouse' => $warehouseName,
            'start_date' => $request->start_date ?: now()->format('Y-m-d'),
            'expired_date' => $request->expired_date,
        ];

        try {
            // Process the Excel file with the form data
            Excel::import(new ItemPricesImport($importOptions), $request->file('excel_file'));

            return redirect()->route('consignment.imports.status', ['batch' => $importBatch])
                ->with('success', 'File uploaded successfully. Click "Process Now" to import the data.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing the Excel file: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the status of an import batch.
     */
    public function showStatus($batch)
    {
        $importService = new ItemPriceImportService();
        $stats = $importService->getBatchStatistics($batch);

        return view('consignment.imports.status', [
            'batch' => $batch,
            'total' => $stats['total'],
            'processed' => $stats['processed'],
            'errors' => $stats['errors'],
            'pending' => $stats['pending'],
            'items' => $stats['items'],
        ]);
    }

    /**
     * Download Excel template for item price upload.
     */
    public function downloadTemplate()
    {
        return Excel::download(new ItemPricesTemplateExport, 'item_prices_template.xlsx');
    }

    /**
     * Process the import batch.
     */
    public function processBatch($batch)
    {
        $importService = new ItemPriceImportService();
        $stats = $importService->processBatch($batch);

        if ($stats['errors'] > 0) {
            return redirect()->route('consignment.imports.status', ['batch' => $batch])
                ->with('warning', 'Processing completed with ' . $stats['errors'] . ' errors. Please check the details.');
        }

        return redirect()->route('consignment.item-prices.index')
            ->with('success', 'Successfully imported ' . $stats['processed'] . ' items.');
    }
}

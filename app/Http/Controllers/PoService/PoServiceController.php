<?php

namespace App\Http\Controllers\PoService;

use App\Http\Controllers\Controller;
use App\Models\PoService;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ItemService;
use Barryvdh\DomPDF\Facade\Pdf;

class PoServiceController extends Controller
{
    public function index()
    {
        return view('po_service.index');
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('po_service.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_no' => 'required|unique:po_services',
            'date' => 'required|date',
            'vendor_code' => 'required|exists:suppliers,code',
            'project_code' => 'required',
            'is_vat' => 'boolean',
            'remarks' => 'nullable|string'
        ]);

        $poService = PoService::create([
            'po_no' => $request->po_no,
            'date' => $request->date,
            'vendor_code' => $request->vendor_code,
            'project_code' => $request->project_code,
            'is_vat' => $request->is_vat ?? true,
            'remarks' => $request->remarks,
            'created_by' => Auth::user()->name
        ]);

        return redirect()->route('po_service.index')
            ->with('success', 'PO Service created successfully.');
    }

    public function show(PoService $po_service)
    {
        return view('po_service.show', compact('po_service'));
    }

    public function edit(PoService $po_service)
    {
        $suppliers = Supplier::all();
        return view('po_service.edit', compact('po_service', 'suppliers'));
    }

    public function update(Request $request, PoService $po_service)
    {
        $request->validate([
            'po_no' => 'required|unique:po_services,po_no,' . $po_service->id,
            'date' => 'required|date',
            'vendor_code' => 'required|exists:suppliers,code',
            'project_code' => 'required',
            'is_vat' => 'boolean',
            'remarks' => 'nullable|string'
        ]);

        $po_service->update([
            'po_no' => $request->po_no,
            'date' => $request->date,
            'vendor_code' => $request->vendor_code,
            'project_code' => $request->project_code,
            'is_vat' => $request->is_vat ?? true,
            'remarks' => $request->remarks,
            'updated_by' => Auth::user()->name
        ]);

        return redirect()->route('po_service.index')
            ->with('success', 'PO Service updated successfully.');
    }

    public function destroy(PoService $po_service)
    {
        $po_service->update(['deleted_by' => Auth::user()->name]);
        $po_service->delete();

        return redirect()->route('po_service.index')
            ->with('success', 'PO Service deleted successfully.');
    }

    public function data()
    {
        $poServices = PoService::with('supplier')->get();

        return DataTables::of($poServices)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier ? $row->supplier->name : '-';
            })
            ->addColumn('action', function ($row) {
                return view('po_service.action', compact('row'));
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addItems($id)
    {   
        $po = PoService::findOrFail($id);
        $vendor = Supplier::where('code', $po->vendor_code)->first() ?? (object)['name' => 'n/a'];
        
        $item_services = ItemService::where('po_service_id', $id)->get();

        $subtotal = 0;
        if($item_services) {
            foreach($item_services as $item) {
                $subtotal += ($item->qty * $item->unit_price);
            }
        }

        return view('po_service.add_items', compact('po', 'vendor', 'item_services'));
    }

    public function preview($id)
    {
        $po = PoService::findOrFail($id);
        $vendor = Supplier::where('code', $po->vendor_code)->first();
        $item_services = \App\Models\ItemService::where('po_service_id', $id)->get();

        $subtotal = 0;
        if($item_services) {
            foreach($item_services as $item) {
                $subtotal += ($item->qty * $item->unit_price);
            }
        }

        return view('po_service.preview', compact('po', 'vendor', 'item_services'));
    }

    public function printPdf($id)
    {
        $po = PoService::findOrFail($id);
        $vendor = Supplier::where('code', $po->vendor_code)->first();
        $item_services = \App\Models\ItemService::where('po_service_id', $id)->get();

        $pdf = Pdf::loadView('po_service.preview', compact('po', 'vendor', 'item_services'));
        return $pdf->stream('PO_Service_'.$po->po_no.'.pdf');
        // Atau gunakan ->download() jika ingin langsung download
    }

    public function summary($po_service)
    {
        $po = PoService::findOrFail($po_service);
        $item_services = ItemService::where('po_service_id', $po_service)->get();

        $sub_total = $item_services->sum(function($item) {
            return $item->qty * $item->unit_price;
        });
        $vat = $po->is_vat == 1 ? $sub_total * 0.11 : 0;
        $total = $po->is_vat == 1 ? $sub_total * 1.11 : $sub_total;

        return response()->json([
            'sub_total' => number_format($sub_total, 2),
            'vat' => number_format($vat, 2),
            'total' => number_format($total, 2),
        ]);
    }
} 
 
<?php

namespace App\Http\Controllers\PoService;

use App\Http\Controllers\Controller;
use App\Models\ItemService;
use App\Models\PoService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ItemServiceController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'po_service_id' => 'required|exists:po_services,id',
                'item_code' => 'required|string|max:255',
                'item_desc' => 'required|string|max:255',
                'qty' => 'required|integer|min:1',
                'uom' => 'required|string|max:50',
                'unit_price' => 'required|numeric|min:0',
            ]);

            // Check if PO is already printed more than 2 times
            $po = PoService::findOrFail($request->po_service_id);
            if ($po->print_count > 2) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add items: PO has been printed more than 2 times'
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', 'Cannot add items: PO has been printed more than 2 times');
            }

            // Create the item
            $item = ItemService::create([
                'po_service_id' => $request->po_service_id,
                'item_code' => $request->item_code,
                'item_desc' => $request->item_desc,
                'qty' => $request->qty,
                'uom' => $request->uom,
                'unit_price' => $request->unit_price,
                'created_by' => auth()->user()->name,
                'updated_by' => auth()->user()->name
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item has been added successfully',
                    'data' => $item
                ]);
            }

            return redirect()->route('po_service.add_items', $request->po_service_id)
                ->with('success', 'Item has been added successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add item: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('po_service.add_items', $request->po_service_id)
                ->with('error', 'Failed to add item: ' . $e->getMessage());
        }
    }

    public function deleteAll($po_service_id)
    {
        try {
            // Check if PO exists
            $po = PoService::findOrFail($po_service_id);
            
            // Check if PO is already printed more than 2 times
            if ($po->print_count > 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete items: PO has been printed more than 2 times'
                ], 422);
            }

            // Delete all items
            ItemService::where('po_service_id', $po_service_id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'All items have been deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importItem(Request $request)
    {
        $request->validate([
            'file_upload' => 'required|file|mimes:xls,xlsx',
            'po_service_id' => 'required|exists:po_services,id',
        ]);

        try {
            $file = $request->file('file_upload');
            $po_service_id = $request->input('po_service_id');
            if (empty($po_service_id)) {
                return redirect()->back()->with('error', 'PO Service ID tidak ditemukan!');
            }

            $imported = [];
            // Ambil data dengan heading row
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0];
            $header = array_map('strtolower', $rows[0]); // header baris pertama, lowercase
            foreach (array_slice($rows, 1) as $row) {
                $data = array_combine($header, $row);
                if (empty($data['item_code'])) continue;
                $imported[] = \App\Models\ItemService::create([
                    'po_service_id' => $po_service_id,
                    'item_code'     => $data['item_code'],
                    'item_desc'     => $data['item_desc'] ?? '',
                    'qty'           => $data['qty'] ?? 1,
                    'uom'           => $data['uom'] ?? '',
                    'unit_price'    => $data['unit_price'] ?? 0,
                    'created_by'    => auth()->user()->name,
                    'updated_by'    => auth()->user()->name,
                ]);
            }

            return redirect()->route('po_service.add_items', ['po_service' => $po_service_id])
                ->with('success', count($imported) . ' items have been imported successfully');
        } catch (\Exception $e) {
            return redirect()->route('po_service.add_items', ['po_service' => $po_service_id])
                ->with('error', 'Failed to import items: ' . $e->getMessage());
        }
    }

    public function data(Request $request)
    {
        $po_service_id = $request->input('po_service_id');
        if (!$po_service_id) {
            return response()->json(['error' => true, 'message' => 'PO Service ID is required'], 400);
        }
        $items = ItemService::where('po_service_id', $po_service_id)->get();
        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('item_amount', fn($item) => number_format($item->qty * $item->unit_price, 2))
            ->addColumn('action', function ($item) {
                return view('item_service.action', compact('item'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit($id)
    {
        try {
            $item = ItemService::findOrFail($id);
            return response()->json($item);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'item_code' => 'required|string|max:255',
                'item_desc' => 'required|string|max:255',
                'qty' => 'required|integer|min:1',
                'uom' => 'required|string|max:50',
                'unit_price' => 'required|numeric|min:0',
            ]);

            $item = ItemService::findOrFail($id);
            
            // Check if PO is already printed more than 2 times
            if ($item->po_service->print_count > 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit item: PO has been printed more than 2 times'
                ], 422);
            }

            $item->update([
                'item_code' => $request->item_code,
                'item_desc' => $request->item_desc,
                'qty' => $request->qty,
                'uom' => $request->uom,
                'unit_price' => $request->unit_price,
                'updated_by' => auth()->user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item has been updated successfully',
                'data' => $item
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = ItemService::findOrFail($id);
            
            // Check if PO is already printed more than 2 times
            if ($item->po_service->print_count > 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete item: PO has been printed more than 2 times'
                ], 422);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item has been deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function model(array $row)
    {
        // Validasi sederhana
        if (!$row['item_code'] || !$row['item_desc'] || !$row['qty'] || !$row['uom'] || !$row['unit_price']) {
            return null; // Baris ini akan di-skip
        }

        return new ItemService([
            'po_service_id' => $this->po_service_id, // dari controller, bukan dari excel
            'item_code'     => $row['item_code'],
            'item_desc'     => $row['item_desc'],
            'qty'           => $row['qty'],
            'uom'           => $row['uom'],
            'unit_price'    => $row['unit_price'],
            'amount'        => $row['qty'] * $row['unit_price'],
        ]);
    }
} 
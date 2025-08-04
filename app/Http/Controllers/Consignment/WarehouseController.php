<?php

namespace App\Http\Controllers\Consignment;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function __construct()
    {
        // Permissions are handled in the routes file
    }

    /**
     * Display a listing of the warehouses.
     */
    public function index()
    {
        $warehouses = Warehouse::orderBy('name')->paginate(10);
        return view('consignment.warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        return view('consignment.warehouses.create');
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:warehouses,code',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Warehouse::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
            ]);

            return redirect()->route('consignment.warehouses.index')
                ->with('success', 'Warehouse created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while creating the warehouse: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified warehouse.
     */
    public function show($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return view('consignment.warehouses.show', compact('warehouse'));
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return view('consignment.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:warehouses,code,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $warehouse->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
            ]);

            return redirect()->route('consignment.warehouses.index')
                ->with('success', 'Warehouse updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating the warehouse: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified warehouse from storage.
     */
    public function destroy($id)
    {
        $warehouse = Warehouse::findOrFail($id);

        try {
            $warehouse->delete();
            return redirect()->route('consignment.warehouses.index')
                ->with('success', 'Warehouse deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the warehouse: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;

class SupplierController extends Controller
{
    public function index()
    {
        return view('suppliers.index');
    }

    public function data()
    {
        $suppliers = Supplier::orderBy('id', 'desc')->get();

        return DataTables::of($suppliers)
            ->addIndexColumn()
            ->addColumn('action', function ($supplier) {
                return view('suppliers.action', compact('supplier'));
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:suppliers',
            'name' => 'required|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'project' => 'nullable|string|max:10',
        ]);

        Supplier::create($request->all());

        Alert::success('Success', 'Supplier created successfully');

        return redirect()->route('suppliers.index');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:suppliers,code,' . $supplier->id,
            'name' => 'required|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'project' => 'nullable|string|max:10',
        ]);

        $supplier->update($request->all());

        Alert::success('Success', 'Supplier updated successfully');

        return redirect()->route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        Alert::success('Success', 'Supplier deleted successfully');

        return redirect()->route('suppliers.index');
    }
} 
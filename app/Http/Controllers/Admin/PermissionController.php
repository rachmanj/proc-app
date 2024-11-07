<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;

class PermissionController extends Controller
{
    public function index()
    {
        return view('admin.permissions.index');
    }

    public function data()
    {
        $permissions = Permission::orderBy('id', 'desc')->get();

        return DataTables::of($permissions)
            ->addIndexColumn()
            ->addColumn('action', function ($permission) {
                return view('admin.permissions.action', compact('permission'));
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update($request->only('name'));

        Alert::success('Success', 'Permission updated successfully');

        return redirect()->route('admin.permissions.index');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        Alert::success('Success', 'Permission deleted successfully');

        return redirect()->route('admin.permissions.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255',
        ]);

        Permission::create($request->only('name', 'guard_name'));

        Alert::success('Success', 'Permission created successfully');

        return redirect()->route('admin.permissions.index');
    }
}

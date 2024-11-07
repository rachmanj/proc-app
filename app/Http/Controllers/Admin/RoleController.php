<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    protected $role;
    protected $permission;

    public function __construct(Role $role, Permission $permission)
    {
        $this->role = $role;
        $this->permission = $permission;
    }

    public function index()
    {
        return view('admin.roles.index');
    }

    public function create()
    {
        $permissions = $this->permission->orderBy('name', 'asc')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        $role = $this->role->create($request->only('name', 'guard_name'));

        $this->syncPermissions($role, $request->permissions);

        Alert::success('Success', 'Role created successfully');

        return redirect()->route('admin.roles.index');
    }

    public function edit($id)
    {
        $role = $this->role->findById($id);
        $permissions = $this->permission->orderBy('name', 'asc')->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $this->validateRequest($request, $id);

        $role = $this->role->findById($id);
        $role->update($request->only('name', 'guard_name'));

        $this->syncPermissions($role, $request->permissions);

        Alert::success('Success', 'Role updated successfully');

        return redirect()->route('admin.roles.index');
    }

    public function destroy($id)
    {
        $role = $this->role->findById($id);

        // Remove all permissions attached to this role
        $role->syncPermissions([]);

        // Delete the role
        $role->delete();

        Alert::success('Success', 'Role deleted successfully');

        return redirect()->route('admin.roles.index');
    }

    public function data()
    {
        $roles = $this->role->orderBy('id', 'desc')->get();

        return DataTables::of($roles)
            ->addIndexColumn()
            ->addColumn('action', 'admin.roles.action')
            ->rawColumns(['action'])
            ->toJson();
    }

    protected function validateRequest(Request $request, $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:roles,name' . ($id ? ',' . $id : ''),
            'guard_name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];

        $request->validate($rules);
    }

    protected function syncPermissions(Role $role, $permissions)
    {
        if ($permissions) {
            $permissionNames = $this->permission->whereIn('id', $permissions)->pluck('name');
            $role->syncPermissions($permissionNames);
        } else {
            $role->syncPermissions([]);
        }
    }
}

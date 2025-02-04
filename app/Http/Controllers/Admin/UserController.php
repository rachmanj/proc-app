<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;


class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'project' => 'required',
            'department_id' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->nik = $request->nik;
        $user->project = $request->project;
        $user->department_id = $request->department_id;
        $user->is_active = 0; //false
        $user->password = bcrypt($request->password);
        $user->save();

        $user->assignRole('user');

        Alert::success('Success', 'User created successfully');

        return redirect()->route('admin.users.index');
    }

    public function activate($id)
    {
        $user = User::find($id);
        $user->is_active = 1; //true
        $user->save();

        Alert::success('Success', 'User activated successfully');

        return redirect()->route('admin.users.index');
    }

    public function deactivate($id)
    {
        $user = User::find($id);

        if ($user->hasRole('superadmin')) {
            Alert::error('Error', 'Superadmin user cannot be deactivated');
            return redirect()->route('admin.users.index');
        }

        $user->is_active = 0; //false
        $user->save();

        Alert::success('Success', 'User deactivated successfully');

        return redirect()->route('admin.users.index');
    }

    public function edit($id)
    {
        $user = User::find($id);
        $userRoles = $user->roles->pluck('name')->toArray();
        $roles = Role::all(); // Add this line to get all roles

        return view('admin.users.edit', compact('user', 'userRoles', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'project' => 'required',
            'department_id' => 'required',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->nik = $request->nik;
        $user->project = $request->project;
        $user->department_id = $request->department_id;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        // Update roles
        if ($request->has('roles')) {
            $roleNames = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
            $user->syncRoles($roleNames);
        } else {
            $user->syncRoles([]); // Clear roles if none are selected
        }

        // Update approval levels
        $existingLevels = $user->approvers->pluck('approval_level_id')->toArray();
        $newLevels = $request->approval_levels ?? [];

        // Remove old approval levels that are not in the new selection
        $user->approvers()->whereNotIn('approval_level_id', $newLevels)->delete();

        // Add new approval levels
        foreach ($newLevels as $levelId) {
            if (!in_array($levelId, $existingLevels)) {
                $user->approvers()->create([
                    'approval_level_id' => $levelId
                ]);
            }
        }

        Alert::success('Success', 'User updated successfully');

        return redirect()->route('admin.users.index');
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user->role == 'superadmin') {
            Alert::error('Error', 'Superadmin user cannot be deleted');
            return redirect()->route('admin.users.index');
        }

        $user->delete();

        Alert::success('Success', 'User deleted successfully');

        return redirect()->route('admin.users.index');
    }

    public function data()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return datatables()->of($users)
            ->addIndexColumn()
            ->editColumn('is_active', function ($user) {
                if ($user->is_active == 1) {
                    return '<span class="badge badge-success">Active</span>';
                } else {
                    return '<span class="badge badge-danger">Inactive</span>';
                }
            })
            ->addColumn('department', function ($user) {
                return $user->department->department_name;
            })
            ->addColumn('action', 'admin.users.action')
            ->rawColumns(['action', 'is_active'])
            ->toJson();
    }
}

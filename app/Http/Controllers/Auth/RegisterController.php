<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('auth.register.index');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|min:3|max:255',
            'username'              => 'required|string|min:3|max:20|unique:users',
            'password'              => 'required|string|min:6|confirmed',
            'project'               => 'required|string',
            'department_id'         => 'required|integer|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name'          => $request->name,
            'username'      => $request->username,
            'password'      => Hash::make($request->password),
            'project'       => $request->project,
            'department_id' => $request->department_id,
        ]);

        $user->assignRole('user');

        return redirect()->route('login')->with('success', 'User created successfully');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index()
    {
        // Fetch users who are NOT standard customers ('user')
        $staff = User::whereIn('role', ['admin', 'manager', 'staff'])->orderBy('id', 'DESC')->get();
        return view('backend.staff.index')->with('staff', $staff);
    }

    public function create()
    {
        $roles = Role::all();
        return view('backend.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string',
            'base_salary' => 'nullable|numeric',
            'overtime_rate' => 'nullable|numeric',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        
        $user = User::create($data);

        if ($user) {
            $user->assignRole($request->role);
            request()->session()->flash('success', 'Staff member added successfully');
        } else {
            request()->session()->flash('error', 'Error occurred while adding staff');
        }
        return redirect()->route('staff.index');
    }

    public function edit($id)
    {
        $staff = User::findOrFail($id);
        $roles = Role::all();
        return view('backend.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'role' => 'required',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string',
            'base_salary' => 'nullable|numeric',
            'overtime_rate' => 'nullable|numeric',
        ]);

        $data = $request->all();
        if($request->password){
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $status = $staff->fill($data)->save();

        if ($status) {
            $staff->syncRoles($request->role);
            request()->session()->flash('success', 'Staff member updated successfully');
        } else {
            request()->session()->flash('error', 'Error occurred while updating staff');
        }
        return redirect()->route('staff.index');
    }

    public function destroy($id)
    {
        $staff = User::findOrFail($id);
        if($staff->id == auth()->user()->id){
            request()->session()->flash('error', 'You cannot delete yourself!');
            return back();
        }
        $status = $staff->delete();
        if ($status) {
            request()->session()->flash('success', 'Staff member deleted successfully');
        } else {
            request()->session()->flash('error', 'Error occurred while deleting staff');
        }
        return redirect()->route('staff.index');
    }
}

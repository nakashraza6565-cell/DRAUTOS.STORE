<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('backend.role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            $parts = explode('-', $permission->name);
            $module = count($parts) > 1 ? $parts[1] : 'general';
            $groupedPermissions[$module][] = $permission;
        }
        return view('backend.role.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        request()->session()->flash('success', 'Role created successfully');
        return redirect()->route('roles.index');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            $parts = explode('-', $permission->name);
            $module = count($parts) > 1 ? $parts[1] : 'general';
            $groupedPermissions[$module][] = $permission;
        }

        return view('backend.role.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array'
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        $role->syncPermissions($request->permissions);

        request()->session()->flash('success', 'Role updated successfully');
        return redirect()->route('roles.index');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'admin') {
            request()->session()->flash('error', 'Cannot delete admin role');
            return redirect()->back();
        }
        $role->delete();
        request()->session()->flash('success', 'Role deleted successfully');
        return redirect()->route('roles.index');
    }
}

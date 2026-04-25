<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define modules and their permissions
        $modules = [
            'dashboard' => ['view'],
            'media' => ['view', 'manage'],
            'banner' => ['view', 'create', 'edit', 'delete'],
            'category' => ['view', 'create', 'edit', 'delete'],
            'product' => ['view', 'create', 'edit', 'delete'],
            'order' => ['view', 'edit', 'delete', 'print'],
            'die' => ['view', 'create', 'edit', 'delete'],
            'purchase' => ['view', 'create', 'edit', 'delete'],
            'bundle' => ['view', 'create', 'edit', 'delete'],
            'brand' => ['view', 'create', 'edit', 'delete'],
            'shipping' => ['view', 'create', 'edit', 'delete'],
            'coupon' => ['view', 'create', 'edit', 'delete'],
            'post-category' => ['view', 'create', 'edit', 'delete'],
            'post-tag' => ['view', 'create', 'edit', 'delete'],
            'post' => ['view', 'create', 'edit', 'delete'],
            'review' => ['view', 'edit', 'delete'],
            'user' => ['view', 'create', 'edit', 'delete', 'manage'],
            'setting' => ['view', 'edit', 'manage'],
            'cash-register' => ['view', 'manage'],
            'incoming-goods' => ['view', 'manage'],
            'packaging' => ['view', 'manage'],
            'return' => ['view', 'manage'],
            'manufacturing' => ['view', 'manage'],
            'payment-reminder' => ['view', 'manage'],
            'customer-ledger' => ['view', 'manage'],
            'cheque' => ['view', 'manage'],
            'task' => ['view', 'manage'],
            'hr' => ['view', 'manage'],
            'analytics' => ['view'],
            'report' => ['view'],
            'expense' => ['view', 'manage'],
            'system-control' => ['view', 'manage'],
        ];

        $allPermissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = $action . '-' . $module;
                Permission::findOrCreate($permissionName);
                $allPermissions[] = $permissionName;
            }
        }

        // Create Roles
        $adminRole = Role::findOrCreate('admin');
        $staffRole = Role::findOrCreate('staff');

        // Assign all permissions to Admin
        $adminRole->syncPermissions($allPermissions);

        // Staff permissions (Dashboard, View Orders, Die Management, etc.)
        $staffPermissions = [
            'view-dashboard',
            'view-order',
            'print-order',
            'view-die',
            'create-die',
            'edit-die',
            'view-purchase',
            'create-purchase',
            'view-bundle',
            'view-product',
            'view-cash-register'
        ];
        $staffRole->syncPermissions($staffPermissions);

        // Assign Admin role to existing admin users if any
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        } else {
            // If no admin user found by the 'role' column, maybe we should assign it to the first user
            $firstUser = User::first();
            if ($firstUser) {
                $firstUser->assignRole('admin');
            }
        }
    }
}

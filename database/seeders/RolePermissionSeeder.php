<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'dashboard',
            'pos',
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Product management
            'view products',
            'create products',
            'edit products',
            'delete products',
            'manage stock',

            // Category management
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Brand management
            'view brands',
            'create brands',
            'edit brands',
            'delete brands',

            // Customer management
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',

            // Supplier management
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',

            // Order management
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'process orders',
            'cancel orders',

            // Purchase management
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',
            'receive purchases',

            // Payment management
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',

            // Expense management
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',

            // Report management
            'view reports',
            'generate reports',

            // Settings
            'manage settings',
            'view activity logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles first
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $cashierRole = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $inventoryRole = Role::firstOrCreate(['name' => 'Inventory Clerk', 'guard_name' => 'web']);
        $salesRole = Role::firstOrCreate(['name' => 'Sales Representative', 'guard_name' => 'web']);

        // Assign permissions to roles
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'view roles',
            'view products',
            'create products',
            'edit products',
            'view categories',
            'create categories',
            'edit categories',
            'view brands',
            'create brands',
            'edit brands',
            'view customers',
            'create customers',
            'edit customers',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'view orders',
            'create orders',
            'edit orders',
            'process orders',
            'view purchases',
            'create purchases',
            'edit purchases',
            'receive purchases',
            'view payments',
            'create payments',
            'edit payments',
            'view expenses',
            'create expenses',
            'edit expenses',
            'view reports',
            'generate reports',
            'manage settings',
            'view activity logs',
        ]);

        $managerRole->givePermissionTo([
            'view products',
            'create products',
            'edit products',
            'manage stock',
            'view categories',
            'create categories',
            'edit categories',
            'view brands',
            'create brands',
            'edit brands',
            'view customers',
            'create customers',
            'edit customers',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'view orders',
            'create orders',
            'edit orders',
            'process orders',
            'view purchases',
            'create purchases',
            'edit purchases',
            'receive purchases',
            'view payments',
            'create payments',
            'view expenses',
            'create expenses',
            'view reports',
        ]);

        $cashierRole->givePermissionTo([
            'pos',
        ]);

        $inventoryRole->givePermissionTo([
            'view products',
            'manage stock',
            'view categories',
            'view brands',
            'view suppliers',
            'view purchases',
            'receive purchases',
        ]);

        $salesRole->givePermissionTo([
            'view products',
            'view customers',
            'create customers',
            'edit customers',
            'view orders',
            'create orders',
            'edit orders',
            'view payments',
            'create payments',
            'view reports',
        ]);

        // Assign roles to users - check if users exist first
        $user = User::where('email', 'admin@example.com')->first();
        if ($user) {
            $user->assignRole('Super Admin');
        }

        $user = User::where('email', 'manager@example.com')->first();
        if ($user) {
            $user->assignRole('Manager');
        }

        $user = User::where('email', 'cashier@example.com')->first();
        if ($user) {
            $user->assignRole('Cashier');
        }

        $user = User::where('email', 'inventory@example.com')->first();
        if ($user) {
            $user->assignRole('Inventory Clerk');
        }

        $user = User::where('email', 'sales@example.com')->first();
        if ($user) {
            $user->assignRole('Sales Representative');
        }
    }
}

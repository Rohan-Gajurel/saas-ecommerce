<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = ['Super Admin', 'Admin', 'Staff'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create product permissions
        $productPermissions = ['products.view', 'products.create', 'products.update', 'products.delete'];
        foreach ($productPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'group' => 'Products Management']);
        }

        // Create category permissions
        $categoryPermissions = ['category.view', 'category.create', 'category.update', 'category.delete'];
        foreach ($categoryPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'group' => 'Category Management']);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'Admin')->first();
        $staffRole = Role::where('name', 'Staff')->first();

        $adminRole->syncPermissions(['products.view', 'products.create', 'products.update', 'products.delete', 'category.view', 'category.create', 'category.update', 'category.delete']);
        $staffRole->syncPermissions(['products.view', 'category.view']);
        
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Permissions
        $permissions = [
            'view products',
            'manage cart',
            'place orders',
            'make payments',
            'view own orders',
            'manage addresses',
            'receive notifications',
            'manage products',
            'manage orders',
            'manage categories',
            'manage inventory',
            'manage media',
            'manage banners',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $client = Role::firstOrCreate(['name' => 'client']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);

        // Assign permissions to roles
        $client->syncPermissions([
            'view products',
            'manage cart',
            'place orders',
            'make payments',
            'view own orders',
            'manage addresses',
            'receive notifications',
        ]);

        $admin->syncPermissions([
            'view products',
            'manage products',
            'manage orders',
            'manage categories',
            'manage inventory',
            'manage media',
            'manage banners',
        ]);

        $superAdmin->syncPermissions($permissions); // Super-admin gets all permissions
    }
}
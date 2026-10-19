<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat permissions
        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-categories',
            'manage-products',
            'manage-stock',
            'create-transaction',
            'view-reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. Buat roles + assign permission
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $manajer = Role::firstOrCreate(['name' => 'manajer']);
        $manajer->syncPermissions(['manage-stock', 'view-reports']);

        $kasir = Role::firstOrCreate(['name' => 'kasir']);
        $kasir->syncPermissions(['create-transaction']);

        // 3. Buat user demo
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@kasirku.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->syncRoles(['admin']);

        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@kasirku.test'],
            [
                'name' => 'Kasir 1',
                'password' => Hash::make('password'),
            ]
        );
        $kasirUser->syncRoles(['kasir']);

        $manajerUser = User::firstOrCreate(
            ['email' => 'manajer@kasirku.test'],
            [
                'name' => 'Manajer',
                'password' => Hash::make('password'),
            ]
        );
        $manajerUser->syncRoles(['manajer']);

        $this->command->info('✅ Role, Permission & User berhasil dibuat!');
    }
}
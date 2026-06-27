<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $permissions = [
            'view leave group',
            'create leave group',
            'edit leave group',
            'delete leave group',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo($permissions);

        $employee = Role::firstOrCreate(['name' => 'Employee']);
        $employee->givePermissionTo(['view leave group']);
    }
}

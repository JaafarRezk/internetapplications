<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $permissions = [
        'user' => [
            'view_file',
            'upload_file',
            'check_out_file',
            'check_in_file',
            'view_group',
            'view_system_reports',
           
        ],
        'admin' => [
           
        ],
        'superadmin' => [
           
        ]
    ];

    public function run(): void
    {
        foreach ($this->permissions as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            foreach ($rolePermissions as $permission) {
                $perm = Permission::firstOrCreate(['name' => $permission]);
                $role->givePermissionTo($perm);
            }
        }
    }
}

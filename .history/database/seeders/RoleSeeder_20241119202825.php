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
           
        ],
        'admin' => [
            'admin-view-dashboard',
            'admin-manage-users',
            'admin-manage-files',
            'admin-view-log',
            'admin-view-file-log',
            'admin-approve-file',
            'admin-delete-file'
        ],
        'superadmin' => [
            'superadmin-access',
            'superadmin-manage-roles',
            'superadmin-manage-permissions',
            'superadmin-view-all-logs',
            'superadmin-delete-any-file',
            'superadmin-delete-any-user'
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

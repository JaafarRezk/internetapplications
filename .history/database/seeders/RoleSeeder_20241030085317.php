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
            'user-view-dashboard',
            'user-view-profile',
            'user-edit-profile',
            'user-upload-file',
            'user-download-file'
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
        // Create permissions and assign to each role
        foreach ($this->permissions as $roleName => $rolePermissions) {
            // Create role
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Create permissions if they don't exist and assign them to the role
            foreach ($rolePermissions as $permission) {
                $perm = Permission::firstOrCreate(['name' => $permission]);
                $role->givePermissionTo($perm);
            }
        }
    }
}

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
            'create_group',
            'invite_user_to_group',
            'approve_file_in_group',
            'view_group',
            'view_system_reports',
           
        ],
        'admin' => [
            'view_file',
            'upload_file',
            'edit_file',
            'check_out_file',
            'check_in_file',
            'create_group',
            'invite_user_to_group',
            'approve_file_in_group',
            'view_group',
            'view_system_reports',
            'send_notifications',
        ],
        'superadmin' => [
            'view_file',
            'upload_file',
            'edit_file',
            'delete_file',
            'check_out_file',
            'check_in_file',
            'manage_file',
            'restore_file',
            'create_group',
            'invite_user_to_group',
            'view_group',
            'edit_group',
            'delete_group',
            'approve_file_in_group',
            'view_system_reports',
            'manage_users',
            'manage_roles_and_permissions',
            'send_notifications',
            'manage_backup',
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

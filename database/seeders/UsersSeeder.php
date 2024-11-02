<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersSeeder extends Seeder
{
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

    public function run()
    {
        // Create roles and assign permissions
        foreach ($this->permissions as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => ucfirst($roleName)]);
            
            // Create permissions if they don’t exist and sync with the role
            $permissions = [];
            foreach ($rolePermissions as $permission) {
                $permissions[] = Permission::firstOrCreate(['name' => $permission])->id;
            }
            $role->syncPermissions($permissions);

            // Create users for each role
            if ($roleName === 'superadmin') {
                $user = User::firstOrCreate(
                    ['email' => 'superadmin@admin.com'],
                    ['name' => 'SuperAdmin', 'password' => bcrypt('12345678')]
                );
                $user->assignRole($role);
            } elseif ($roleName === 'admin') {
                $user = User::firstOrCreate(
                    ['email' => 'admin@admin.com'],
                    ['name' => 'Admin', 'password' => bcrypt('12345678')]
                );
                $user->assignRole($role);
            } elseif ($roleName === 'user') {
                $user = User::firstOrCreate(
                    ['email' => 'user@user.com'],
                    ['name' => 'User', 'password' => bcrypt('12345678')]
                );
                $user->assignRole($role);
            }
        }
    }
}

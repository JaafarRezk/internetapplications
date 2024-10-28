<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersSeeder extends Seeder
{
    private $adminPermissions = [
        'admin-view-log',
        'admin-view-file-log',
    ];

    private $userPermissions = [
        'user-view-file-log',
    ];

    private $superAdminPermissions = [
        'superadmin-access',  // SuperAdmin specific permission
        'admin-view-log',
        'admin-view-file-log',
        'user-view-file-log'
    ];

    public function run()
    {
        // Create SuperAdmin
        $superAdminUser = User::firstOrCreate(
            ['name' => 'SuperAdmin'],
            ['email' => 'superadmin@admin.com', 'password' => bcrypt('12345678')]
        );

        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $superAdminRole->syncPermissions($this->superAdminPermissions);
        $superAdminUser->assignRole($superAdminRole);

        // Create Admin
        $adminUser = User::firstOrCreate(
            ['name' => 'Admin'],
            ['email' => 'admin@admin.com', 'password' => bcrypt('12345678')]
        );

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions($this->adminPermissions);
        $adminUser->assignRole($adminRole);

        // Create User role
        $userRole = Role::firstOrCreate(['name' => 'User']);
        $userRole->syncPermissions($this->userPermissions);
    }
}

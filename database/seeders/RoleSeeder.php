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
        'admin-view-log',
        'admin-view-file-log',
        'user-view-file-log',
        'superadmin-access'  // Example permission for SuperAdmin
    ];

    public function run(): void
    {
        // Create all permissions
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}

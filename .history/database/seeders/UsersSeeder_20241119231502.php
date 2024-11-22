<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersSeeder extends Seeder
{
    private $users = [
        'user' => [
            'email' => 'user@user.com',
            'name' => 'User',
            'password' => '12345678',
        ],
      
    ];

    public function run()
    {
        foreach ($this->users as $roleName => $userData) {
            $role = Role::firstOrCreate(['name' => ucfirst($roleName)]);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                ['name' => $userData['name'], 'password' => bcrypt($userData['password'])]
            );

            $user->assignRole($role);
        }
    }
}

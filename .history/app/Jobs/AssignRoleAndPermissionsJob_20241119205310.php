<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Role;

class AssignRoleAndPermissionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $role;
    protected $permissions;

    public function __construct(User $user, string $role, array $permissions)
    {
        $this->user = $user;
        $this->role = $role;
        $this->permissions = $permissions;
    }

    public function handle()
    {
        // Assign role to user
        $role = Role::findByName($this->role);
        $this->user->assignRole($role);

        // Assign permissions to user
        foreach ($this->permissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $this->user->givePermissionTo($perm);
        }
    }
}

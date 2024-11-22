<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $this->user->syncPermissions($this->permissions);

        $this->user->assignRole($this->role);
    }
}

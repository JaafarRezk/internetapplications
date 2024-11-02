<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Exceptions\CreateObjectException;
use Spatie\Permission\Models\Role;

class GroupService extends Service{
  

    public function createGroup($bodyParameters)
    {
        if (!isset($bodyParameters['name'])) {
            throw new CreateObjectException("The 'name' field is required.");
        }

        $parameters = [
            'name' => $bodyParameters['name'],
            'creator_id' => auth()->user()->id,
        ];

        $group = Group::createNewWithValidation($parameters);

        // تعيين صلاحية Admin للمستخدم
        $user = auth()->user();
        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole) {
            $user->assignRole($adminRole);
        }

        return $group;
    }


   
}

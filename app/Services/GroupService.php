<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Exceptions\CreateObjectException;

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

    return $group;
}


   
}

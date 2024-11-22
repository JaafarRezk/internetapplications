<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Exceptions\CreateObjectException;
use Spatie\Permission\Models\Role;

class GroupService extends Service{
    CONST aspects_map = array(
        'createGroup'=>array('TransactionAspect', 'LoggingAspect'),
        'checkFilesOwnership'=>array('TransactionAspect', 'LoggingAspect'),
        'addFilesToGroup'=>array('TransactionAspect', 'LoggingAspect'),
        'addUsersToGroup'=>array('TransactionAspect', 'LoggingAspect'),
        'userGroups'=>array('TransactionAspect', 'LoggingAspect'),

        
    );

    public function userGroups($id){
        return Group::where('creator_id', $id)->get();
    }

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

    public function checkFilesOwnership($ids_arr)
    {
        foreach($ids_arr as $id){
            $file = File::fetchByIdWithCacheAndAuth($id);
            if($file->user_id != auth()->user()->id){
                return false;
            }
        }
        return true;
    }

    public function addFilesToGroup($bodyParameters)
    {
        $group = Group::fetchByIdWithCacheAndAuth($bodyParameters['group_id']);
        $ids = $bodyParameters['files_ids'][0];
        $ids_arr = preg_split ("/\,/", $ids);

        if($this->checkFilesOwnership($ids_arr)){
            $group->files()->syncWithoutDetaching($ids_arr);
            return true;
        }else{
            return null;
        }
    }

    public function addUsersToGroup($bodyParameters)
    {
        $group = Group::fetchByIdWithCacheAndAuth($bodyParameters['group_id']);
        $ids = $bodyParameters['users_ids'][0];
        $ids_arr = preg_split ("/\,/", $ids);
        $group->users()->syncWithoutDetaching($ids_arr);

        return true;
    }


   
}

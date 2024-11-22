<?php

namespace App\Services;

use App\Models\File;
use App\Models\Group;
use App\Exceptions\CreateObjectException;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

class GroupService extends Service{
    CONST aspects_map = array(
        'createGroup'=>array('TransactionAspect', 'LoggingAspect'),
        'checkFilesOwnership'=>array('TransactionAspect', 'LoggingAspect'),
        'addFilesToGroup'=>array('TransactionAspect', 'LoggingAspect'),
        'addUsersToGroup'=>array('TransactionAspect', 'LoggingAspect'),
        
    );


    public function createGroup($bodyParameters)
    {
        $parameters = [
            'name' => $bodyParameters['name'],
            'creator_id' => auth()->user()->id,
        ];
        $group = Group::createNewWithValidation($parameters);
        return $group;
    }


    public function myGroups($id){
        return Group::where('creator_id', $id)->get();
    }

    

    public function addFilesToGroup($bodyParameters)
    {
        $group = Group::fetchByIdWithCacheAndAuth($bodyParameters['group_id']);
        $ids = $bodyParameters['files_ids'][0];
        $ids_arr = preg_split("/,/", $ids);
    
        // تحقق من ملكية الملفات وصلاحيات المستخدم
        foreach ($ids_arr as $id) {
            $file = File::find($id); // اجلب الملف
            if (!$file || !Gate::allows('viewFile', $file)) {
                throw new \Exception("Unauthorized to add file ID {$id}");
            }
        }
    
        $group->files()->syncWithoutDetaching($ids_arr);
        return true;
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


    public function addUsersToGroup($bodyParameters)
    {
        $group = Group::fetchByIdWithCacheAndAuth($bodyParameters['group_id']);
        $ids = $bodyParameters['users_ids'][0];
        $ids_arr = preg_split ("/\,/", $ids);
        $group->users()->syncWithoutDetaching($ids_arr);

        return true;
    }


   
}

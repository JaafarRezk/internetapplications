<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilePolicy
{
    public function readFile(User $user, $obj): bool
    {
        if($obj instanceof File)
            return $user->groups->intersect($obj->groups)->isNotEmpty() || $obj->user_id == auth()->user()->id;
        else
            return true;
    }
    public function checkIn(User $user, $obj): bool
    {
        if($obj instanceof File)
            return $user->groups->intersect($obj->groups)->isNotEmpty() || $obj->user_id == auth()->user()->id;
        else
            return true;
    }

    public function removeFiles(User $user, $obj): bool
    {
        if($obj instanceof File)
            return $user->id == $obj->user_id;
        else
            return true;
    }

    public function checkOut(User $user, $obj): bool
    {
        if($obj instanceof File)
            return $user->id == $obj->file_holder_id;
        else
            return true;
    }

    public function view(User $user, File $file): bool
    {
        // إذا كان الملف لا ينتمي إلى أي مجموعة، يمكن للجميع رؤيته
        if ($file->groups->isEmpty()) {
            return true;
        }
    
        // تحقق مما إذا كان المستخدم عضوًا في أي مجموعة من المجموعات التي ينتمي إليها الملف
        foreach ($file->groups as $group) {
            if ($user->groups->contains('id', $group->id)) {
                return true;  // السماح بالعرض إذا كان المستخدم عضوًا في المجموعة
            }
        }
    
        // إذا لم يكن المستخدم عضوًا في أي مجموعة من المجموعات، يتم منع العرض
        return false;
    }
    
}

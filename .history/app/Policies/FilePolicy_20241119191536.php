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

    public function view(User $user, File $file)
    {
        // تحقق إذا كان الملف ينتمي لأي مجموعة
        if ($file->groups()->exists() && $file->) {
            return false;
        }

        // تحقق إذا كان المستخدم عضوًا في أي من مجموعات الملف
        return $file->groups()->whereIn('id', $user->groups->pluck('id'))->exists();
    }
}

<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function addFilesToGroup(User $user, $obj): bool
    {
        if($obj instanceof Group)
            return $user->groups->contains("id",$obj->id);
        else
            return true;
    }

    public function removeFilesFromGroup(User $user, $obj): bool
    {
        if($obj instanceof Group)
            return $user->groups->contains("id",$obj->id);
        else
            return true;
    }

    public function removeUsersFromGroup(User $user, $obj): bool
    {
        if($obj instanceof Group)
            return $user->id == $obj->creator_id;
        else
            return true;
    }

    public function addUsersToGroup(User $user, $obj): bool
    {
        if($obj instanceof Group)
            return $user->id == $obj->creator_id;
        else
            return true;
    }

    public function removeGroup(User $user, $obj): bool
    {
        if($obj instanceof Group)
            return $user->id == $obj->creator_id;
        else
            return true;
    }

    public function viewFileInGroup(User $user, File $file): bool
    {
        $group = $file->group; 
        if ($group) {
            // تحقق إذا كان المستخدم عضوًا في المجموعة
            return $group->users->contains($user->id);
        }

        // إذا لم يكن الملف مرتبطًا بمجموعة، السماح بالعرض
        return true;
    }

}

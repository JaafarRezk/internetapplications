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

    public function viewFile(User $user, File $file): bool
    {
        // التحقق من وجود تقاطع بين مجموعات المستخدم ومجموعات الملف
        return $file->groups->isEmpty() || $user->groups->intersect($file->groups)->isNotEmpty();
    }
}

    /**
     * Update the model with conditions, invalidate the cache if successful.
     */
    public function updateWithConditions($data = [], $conditions = [])
    {
        $class = get_called_class();
        $id = $this->id;
        $key = $class . $id;

        Cache::forget($key);

        $res = $this->where($conditions + ['id' => $this->id])->update($data);
        if ($res < 1) {
            throw new ObjectNotFoundException($class . ' object not found');
        }
        return $res;
    }

    /**
     * Delete the model and clear cache.
     */
    public function deleteWithCache()
    {
        self::removeCacheForIds([$this->id]);

        $res = $this->delete();

        if ($res < 1) {
            throw new ObjectNotFoundException(get_called_class() . ' object not found');
        }

        return $res;
    }

    /**
     * Remove cache entries for an array of IDs.
     */
    public static function removeCacheForIds($id_array)
    {
        $class = get_called_class();
        foreach ($id_array as $id) {
            $key = $class . $id;
            Cache::forget($key);
        }
    }

    /**
     * Create a new instance with validation and caching.
     */
    public static function createNewWithValidation($parameters)
    {
        $class_name = get_called_class();
        $class = new $class_name();
        $validation_rules = $class->validation_rules;

        $validator = Validator::make($parameters, $validation_rules);

        if (!$validator->fails()) {
            $obj = $class::create($parameters);

            return $obj;
        } else {
            throw new CreateObjectException($validator->errors()->first());
        }
    }
}

<?php

namespace App\Models;

use App\Exceptions\CreateObjectException;
use App\Exceptions\ObjectNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class GenericModel extends Model
{
    use HasFactory;

    public $validation_rules = [];

    /**
     *
     * @param int $id
     * @return Model
     * @throws ObjectNotFoundException
     * @throws \Exception
     */
    public static function fetchByIdWithCacheAndAuth($id)
    {
        $class = get_called_class();
        $key = $class . $id;

        $object = Cache::get($key);

        if ($object == null) {
            $object = self::find($id);
            
            if ($object == null) {
                throw new ObjectNotFoundException($class . ' object not found');
            } else {
                Cache::add($key, $object, env('CACHE_EXPIRY'));
            }
        }

        $trace = debug_backtrace();
        $policy = Gate::getPolicyFor($class);

        if (method_exists($policy, $trace[1]['function']) && !auth()->user()->can($trace[1]['function'], $object)) {
            throw new \Exception("Unauthorized access");
        }

        return $object;
    }

    /**
     *
     * @param array $data البيانات المراد تحديثها
     * @param array $conditions شرط مخصص (اختياري)
     * @return int عدد السجلات التي تم تحديثها
     * @throws ObjectNotFoundException
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
     *
     * @return int نتيجة الحذف (1 إذا تم بنجاح، 0 إذا فشل)
     * @throws ObjectNotFoundException
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
     *
     * @param array $id_array مصفوفة من المعرفات (IDs) للكائنات المراد حذفها من التخزين المؤقت
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
     *
     * @param array $parameters البيانات المطلوبة لإنشاء الكائن
     * @return Model الكائن الذي تم إنشاؤه
     * @throws CreateObjectException
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

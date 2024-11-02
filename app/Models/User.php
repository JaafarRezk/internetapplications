<?php
namespace App\Models;

use App\Exceptions\CreateObjectException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public $validation_rules = [
        'name' => 'required|string',
        'email' => 'required|string|unique:users,email',
        'password' => 'required|string',
    ];

    private $userPermissions = [
        'user-view-dashboard',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_users', 'user_id')->withTimestamps();
    }

    public static function createUserWithDefaultPermissionsAndRole($parameters)
    {
        $class_name = get_called_class();
        $class = new $class_name();
        $validation_rules = $class->validation_rules;

        $validator = Validator::make($parameters, $validation_rules);

        if (!$validator->fails()) {
            return DB::transaction(function () use ($class, $parameters) {
                $user = $class::create($parameters);

                $user->syncPermissions($class->userPermissions);
                $user->assignRole('User');

                return $user;
            });
        } else {
            throw new CreateObjectException($validator->errors()->first());
        }
    }
}

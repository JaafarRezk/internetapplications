<?php

namespace App\Services;

use App\Exceptions\CreateObjectException;
use App\Exceptions\loginError;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserService extends Service{

    /**
     * @throws loginError
     */
    public function logIn($bodyParameters): array
    {
        $data = Validator::make($bodyParameters, [
            'email' => 'required|email',
            'password' => 'required|string'
        ])->validated();
    
        $user = User::where('email', $data['email'])->first();
    
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new loginError("User doesn't exist or credentials are wrong");
        }
    
        $roles = $user->getRoleNames()->toArray(); 
        $token = $user->createToken('apiToken')->plainTextToken;
    
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $roles, 
            ],
            'token' => $token
        ];
    }
    
       public function logOut(): bool
    {
        auth()->user()->tokens()->delete();
        return true;
    }

    
    public function register($bodyParameters)
    {
        $parameters = [
            'name' => $bodyParameters['name'],
            'email' => $bodyParameters['email'],
            'password' => bcrypt($bodyParameters['password']) 
        ];
    
        return User::createUserWithDefaultPermissionsAndRole($parameters);
    }
    
    

    public function allUsers()
    {
        return User::all();
    }
}


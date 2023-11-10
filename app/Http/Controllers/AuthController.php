<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {
        //validate incoming request data
        $request->validated($request->all());
        
        //check if validated data is inside database
        if(!Auth::attempt($request->only('email', 'password'))){
            return $this->error('', 'Invalid Credentials', 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->success([
            'user'=> $user,
            'token'=>$user->createToken('Api Token of ' . $user->name)->plainTextToken
        ]);

    }

    public function register(StoreUserRequest $request)
    {
        //validate incoming request data
        $request->validated($request->all());

        //create a user
        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password' => Hash::make($request->password),
        ]);


        return $this->success([
            'user'=>$user,
            'token'=> $user->createToken('API Token of ' . $user->name)->plainTextToken
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
            'message'=> 'You have successfuly been logged out'
        ]);
    }
    
}

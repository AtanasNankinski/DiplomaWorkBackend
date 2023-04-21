<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function createAdmin()
    {
        $email = "Admin@admin.com";
        $exists = User::where('email', $email)->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Email already exists',
            ], 422);
        }

        $user = User::create([
            'name' => "Admin",
            'email' => $email,
            'password' => "Admin981104",
            'user_type' => 1,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response(['user' => $user, 'access_token' => $token], 201);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'user_type' => 2,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response(['user' => $user, 'access_token' => $token], 201);
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        return response()->json(['error' => 'Invalid login credentials'], 401);
    }
}

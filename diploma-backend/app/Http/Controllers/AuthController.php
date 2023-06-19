<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Score;
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = User::create([
            'name' => "New User",
            'email' => $request->email,
            'password' => $request->password,
            'user_type' => 2,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $score = Score::create([
            'user' => $user->id,
            'victories' => 0,
            'defeats' => 0,
        ]);
        $score->save();

        return response([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'access_token' => $token
        ], 201);
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'access_token' => $token,
            ], 201);
        }

        return response(['error' => 'Invalid login credentials'], 422);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}

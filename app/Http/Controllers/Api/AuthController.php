<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function teste(Request $request)
    {
        return  "ok";
    }
    
    public function login(Request $request)
    {
       
    
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string'],
        ]);

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken($credentials['device_name'] ?? 'web')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_path ? url("storage/{$user->avatar_path}") : null,
            ],
        ]);
    }

    public function loginAccessToken(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string'],
        ]);

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken($credentials['device_name'] ?? 'web')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_path ? url("storage/{$user->avatar_path}") : null,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->noContent();
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4'],
            'device_name' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken($data['device_name'] ?? 'web')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => null,
            ],
        ], 201);
    }
}

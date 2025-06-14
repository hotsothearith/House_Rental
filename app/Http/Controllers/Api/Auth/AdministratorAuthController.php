<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdministratorAuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => ['required', 'string', 'max:100', 'unique:administrators'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        $admin = Administrator::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        $token = $admin->createToken('admin_auth_token', ['admin'])->plainTextToken;

        return response()->json([
            'message' => 'Administrator registration successful',
            'administrator' => $admin,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

public function login(Request $request)
{
    $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $admin = Administrator::where('username', $request->username)->first();

    if (!$admin || !Hash::check($request->password, $admin->password)) {
        return response()->json(['message' => 'Invalid login credentials'], 401);
    }

    $token = $admin->createToken('admin_auth_token', ['admin'])->plainTextToken;

    return response()->json([
        'message' => 'Administrator login successful',
        'administrator' => $admin,
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
}

    public function logout(Request $request)
    {
        $request->user('sanctum_administrator')->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Administrator logged out successfully'
        ]);
    }
}

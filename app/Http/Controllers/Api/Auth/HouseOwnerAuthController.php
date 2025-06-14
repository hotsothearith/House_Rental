<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\HouseOwner; // Changed from Agent
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class HouseOwnerAuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'email_address' => ['required', 'string', 'email', 'max:100', 'unique:house_owners'],
                'owner_name' => ['required', 'string', 'max:120'], // Changed from agent_name
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'mobile_number' => ['nullable', 'string', 'max:11'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        $houseOwner = HouseOwner::create([ // Changed from Agent::create
            'email_address' => $request->email_address,
            'owner_name' => $request->owner_name, // Changed from agent_name
            'password' => Hash::make($request->password),
            'mobile_number' => $request->mobile_number,
            'address' => $request->address, // Added address field
        ]);

        $token = $houseOwner->createToken('house_owner_auth_token', ['house_owner'])->plainTextToken;

        return response()->json([
            'message' => 'House owner registration successful',
            'house_owner' => $houseOwner, // Changed key
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email_address' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        $houseOwner = HouseOwner::where('email_address', $request->email_address)->first();

        if (!$houseOwner || !Hash::check($request->password, $houseOwner->password)) {
        return response()->json([
        'message' => 'Invalid login credentials'
        ], 401);
}
        $token = $houseOwner->createToken('house_owner_auth_token', ['house_owner'])->plainTextToken;

        return response()->json([
            'message' => 'House owner login successful',
            'house_owner' => $houseOwner, // Changed key
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user('sanctum_house_owner')->currentAccessToken()->delete();

        return response()->json([
            'message' => 'House owner logged out successfully'
        ]);
    }
    public function index()
{
    // Return all registered house owners
    return response()->json(\App\Models\HouseOwner::all());
}
}
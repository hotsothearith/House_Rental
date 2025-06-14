<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TenantAuthController extends Controller
{
    public function register(Request $request)
    {
    $request->validate([
        'full_name' => 'required|string|max:255',
        'email_address' => 'required|email|unique:tenants,email_address',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $tenant = Tenant::create([
        'full_name' => $request->full_name,
        'email_address' => $request->email_address,
        'password' => bcrypt($request->password),
    ]);

        $token = $tenant->createToken('tenant_auth_token', ['tenant'])->plainTextToken;

        return response()->json([
            'message' => 'Tenant registration successful',
            'tenant' => $tenant,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

   public function login(Request $request)
{
    $request->validate([
        'email_address' => 'required|email',
        'password' => 'required',
    ]);

    $tenant = \App\Models\Tenant::where('email_address', $request->email_address)->first();

    if (!$tenant || !\Hash::check($request->password, $tenant->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Create a token for the tenant
    $token = $tenant->createToken('tenant-token')->plainTextToken;

    return response()->json([
        'tenant' => $tenant,
        'token' => $token,
    ]);
}

    public function logout(Request $request)
    {
        $request->user('sanctum_tenant')->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Tenant logged out successfully'
        ]);
    }
}
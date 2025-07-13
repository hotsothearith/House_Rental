<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\Booking;
use App\Models\House;
use App\Models\HouseOwner; // Ensure this is imported
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\AgreementResource;

class AgreementController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can view agreements.'], 403);
        }
        return response()->json(Agreement::with('booking', 'house', 'houseOwner', 'tenant')->paginate(10));
    }


  public function houseOwnerAgreements(Request $request)
{
    $houseOwner = Auth::user(); // Ensure this is an instance of App\Models\HouseOwner

    if (!$houseOwner) {
        return response()->json(['message' => 'House owner not authenticated.'], 401);
    }

    // Check if $houseOwner->id exists
    if (!isset($houseOwner->id)) {
        return response()->json(['message' => 'Server configuration error: House owner ID not found.'], 500);
    }

    // ... rest of your code
}


    public function store(Request $request)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can create agreements.'], 403);
        }

        try {
$validatedData = $request->validate([
    'booking_no' => 'required|exists:bookings,id',
    'house_id' => 'required|exists:houses,id',
    'house_owner_id' => 'required|exists:house_owners,id',
    'user_email' => 'required|email|exists:tenants,email_address',
    'remember' => 'nullable|string',
]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        $agreement = Agreement::create($validatedData);

        return response()->json([
            'message' => 'Agreement created successfully',
            'agreement' => $agreement->load('booking', 'house', 'houseOwner', 'tenant')
        ], 201);
    }


    public function show(Agreement $agreement)
    {
        // Authorization: Only admin, the house owner involved, or the tenant involved can view
        if (Auth::guard('sanctum_administrator')->check() ||
            (Auth::guard('sanctum_house_owner')->check() && Auth::guard('sanctum_house_owner')->user()->id === $agreement->house_owner_id) ||
            (Auth::guard('sanctum_tenant')->check() && Auth::guard('sanctum_tenant')->user()->email_id === $agreement->user_email)) {
            return response()->json($agreement->load('booking', 'house', 'houseOwner', 'tenant'));
        }

        return response()->json(['message' => 'Unauthorized to view this agreement.'], 403);
    }


    public function update(Request $request, Agreement $agreement)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can update agreements.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'booking_no' => 'sometimes|exists:bookings,id',
                'house_id' => 'sometimes|exists:houses,id',
                'house_owner_id' => 'sometimes|exists:house_owners,id',
                'user_email' => 'sometimes|email|exists:tenants,email_id',
                'remember' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        $agreement->update($validatedData);

        return response()->json([
            'message' => 'Agreement updated successfully',
            'agreement' => $agreement->load('booking', 'house', 'houseOwner', 'tenant')
        ]);
    }


    public function destroy(Agreement $agreement)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can delete agreements.'], 403);
        }

        $agreement->delete();

        return response()->json([
            'message' => 'Agreement deleted successfully'
        ], 200);
    }
}

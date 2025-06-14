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

class AgreementController extends Controller
{
    /**
     * Display a listing of agreements (for Admin).
     */
    public function index(Request $request)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can view agreements.'], 403);
        }
        return response()->json(Agreement::with('booking', 'house', 'houseOwner', 'tenant')->paginate(10));
    }

    /**
     * Display a listing of agreements for the authenticated house owner.
     */
    public function houseOwnerAgreements(Request $request)
    {
        if (!Auth::guard('sanctum_house_owner')->check()) {
            return response()->json(['message' => 'Unauthorized: Only house owners can view their agreements.'], 403);
        }

        $houseOwner = Auth::guard('sanctum_house_owner')->user();
        return response()->json(Agreement::where('house_owner_id', $houseOwner->id)->with('booking', 'house', 'tenant')->paginate(10));
    }


    /**
     * Store a newly created agreement.
     * Requires authentication by an Administrator.
     */
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
                'user_email' => 'required|email|exists:tenants,email_id',
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

    /**
     * Display the specified agreement.
     */
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

    /**
     * Update the specified agreement.
     * Requires authentication by an Administrator.
     */
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

    /**
     * Remove the specified agreement.
     * Requires authentication by an Administrator.
     */
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

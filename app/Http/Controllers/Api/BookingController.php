<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\House;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Booking::with('house', 'tenant')->paginate(10));
    }

    public function houseOwnerBookings(Request $request)
    {
        if (!Auth::guard('sanctum_house_owner')->check()) {
            return response()->json(['message' => 'Unauthorized: Only house owners can view their bookings.'], 403);
        }

        $houseOwner = Auth::guard('sanctum_house_owner')->user();
        $bookings = Booking::whereHas('house', function ($query) use ($houseOwner) {
            $query->where('house_owner_id', $houseOwner->id);
        })->with('house', 'tenant')->paginate(10);

        return response()->json($bookings);
    }

    public function indexAdmin(Request $request)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can view all bookings.'], 403);
        }
        return response()->json(Booking::with('house', 'tenant')->paginate(10));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('sanctum_tenant')->check()) {
            return response()->json(['message' => 'Unauthorized: Only tenants can create bookings.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'house_id' => 'required|exists:houses,id',
                'from_date' => 'required|string|max:20',
                'to_date' => 'required|string|max:20',
                'duration' => 'nullable|string|max:20',
                'message' => 'nullable|string|max:255',
                'status' => 'integer|in:0,1,2',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        if (!isset($validatedData['status'])) {
            $validatedData['status'] = 0;
        }

        $tenant = Auth::guard('sanctum_tenant')->user();
        $validatedData['tenant_email'] = $tenant->email_address;
        $validatedData['booking_number'] = rand(100000, 999999);

        $booking = Booking::create($validatedData);

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking->load('house', 'tenant')
        ], 201);
    }

    public function show(Booking $booking)
    {
        if (Auth::guard('sanctum_tenant')->check() && Auth::guard('sanctum_tenant')->user()->email_address === $booking->tenant_email) {
            return response()->json($booking->load('house', 'tenant'));
        }
        if (Auth::guard('sanctum_house_owner')->check() && Auth::guard('sanctum_house_owner')->user()->id === $booking->house->house_owner_id) {
            return response()->json($booking->load('house', 'tenant'));
        }
        if (Auth::guard('sanctum_administrator')->check()) {
            return response()->json($booking->load('house', 'tenant'));
        }

        return response()->json(['message' => 'Unauthorized to view this booking.'], 403);
    }

    public function update(Request $request, Booking $booking)
    {
        if (!Auth::guard('sanctum_tenant')->check() || Auth::guard('sanctum_tenant')->user()->email_address !== $booking->tenant_email) {
            return response()->json(['message' => 'Unauthorized: You can only update your own bookings.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'from_date' => 'sometimes|string|max:20',
                'to_date' => 'sometimes|string|max:20',
                'duration' => 'nullable|string|max:20',
                'message' => 'nullable|string|max:255',
                'status' => 'sometimes|integer|in:0,1,2',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        $booking->update($validatedData);

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $booking->load('house', 'tenant')
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        if (!(Auth::guard('sanctum_house_owner')->check() && Auth::guard('sanctum_house_owner')->user()->id === $booking->house->house_owner_id) &&
            !Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only the house owner or an administrator can update booking status.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'status' => 'required|integer|in:0,1,2',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        $booking->update(['status' => $validatedData['status']]);

        return response()->json([
            'message' => 'Booking status updated successfully',
            'booking' => $booking->load('house', 'tenant')
        ]);
    }

    public function destroy(Booking $booking)
    {
        if (!(Auth::guard('sanctum_tenant')->check() && Auth::guard('sanctum_tenant')->user()->email_address === $booking->tenant_email) &&
            !Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: You can only delete your own bookings or as an administrator.'], 403);
        }

        $booking->delete();

        return response()->json([
            'message' => 'Booking deleted successfully'
        ], 200);
    }
}
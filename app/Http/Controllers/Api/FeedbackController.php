<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Tenant; // Make sure to import the Tenant model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\FeedbackResource; // Ensure you have a FeedbackResource for formatting responses
class FeedbackController extends Controller
{

    public function indexAdmin(Request $request)
    {
        // Check if the authenticated user is an administrator
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can view all feedback.'], 403);
        }
        // Return all feedback entries, eager-loading the associated tenant
        return response()->json(Feedback::with('tenant')->paginate(10));
    }


public function store(Request $request)
{
    if (!Auth::guard('sanctum_tenant')->check()) {
        return response()->json(['message' => 'Unauthorized: Only tenants can provide feedback.'], 403);
    }

    try {
        $validatedData = $request->validate([
            'payment_id' => 'required|exists:payments,id', // Require payment_id
            'comment' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation Error',
            'errors' => $e->errors()
        ], 422);
    }

    $tenant = Auth::guard('sanctum_tenant')->user();
    $validatedData['user_email'] = $tenant->email_address;

    $feedback = Feedback::create($validatedData);

    // Eager load tenant and payment.house
    return response()->json([
        'message' => 'Feedback submitted successfully',
        'feedback' => $feedback->load('tenant', 'payment.house')
    ], 201);
}


    public function show(Feedback $feedback)
    {
        // Check authorization: tenant who submitted it OR an administrator
        if ((Auth::guard('sanctum_tenant')->check() && Auth::guard('sanctum_tenant')->user()->email_id === $feedback->user_email) ||
            Auth::guard('sanctum_administrator')->check()) {
            return response()->json($feedback->load('tenant')); // Return feedback with associated tenant
        }

        // If not authorized, return a 403 Forbidden response
        return response()->json(['message' => 'Unauthorized to view this feedback.'], 403);
    }


    public function update(Request $request, Feedback $feedback)
    {
        // Check if the authenticated user is an administrator
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can update feedback.'], 403);
        }

        try {
            // Validate incoming data; 'sometimes' allows partial updates
            $validatedData = $request->validate([
                'user_email' => 'required|email|exists:tenants,email_address',
                'payment_id' => 'required|exists:payments,id',
                'comment' => 'nullable|string',
                'rating' => 'required|integer|min:1|max:5',
            ]);
        } catch (ValidationException $e) {
            // Return validation errors if any occur
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        // Update the feedback entry
        $feedback->update($validatedData);

        // Return a success response with the updated feedback and its associated tenant
        return response()->json([
            'message' => 'Feedback updated successfully',
            'feedback' => $feedback->load('tenant')
        ]);
    }


    public function destroy(Feedback $feedback)
    {
        // Check if the authenticated user is an administrator
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can delete feedback.'], 403);
        }

        // Delete the feedback entry
        $feedback->delete();

        // Return a success response
        return response()->json([
            'message' => 'Feedback deleted successfully'
        ], 200);
    }
        public function houseOwnerFeedback(Request $request)
    {
        $houseOwner = Auth::user(); // Assuming the authenticated user is the house owner

        if (!$houseOwner) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $feedbacks = Feedback::with(['tenant', 'house']) // Ensure these relationships are defined in your Feedback model
            ->whereHas('house', function ($query) use ($houseOwner) {
                // IMPORTANT: Qualify 'house_owner_id' with the table name 'houses'
                $query->where('houses.house_owner_id', $houseOwner->id);
            })
            ->get(); // This is the line where the query is executed, causing the error.

        // Return the collection using your FeedbackResource
        return FeedbackResource::collection($feedbacks);
    }
}

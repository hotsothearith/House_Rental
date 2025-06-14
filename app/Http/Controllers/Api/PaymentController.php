<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function houseOwnerPayments(Request $request)
    {
        if (!Auth::guard('sanctum_house_owner')->check()) {
            return response()->json(['message' => 'Unauthorized: Only house owners can view their payments.'], 403);
        }

        $houseOwner = Auth::guard('sanctum_house_owner')->user();
        return response()->json(Payment::where('house_owner_id', $houseOwner->id)->with('house', 'tenant', 'houseOwner')->paginate(10));
    }

    public function indexAdmin(Request $request)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can view all payments.'], 403);
        }
        return response()->json(Payment::with('house', 'tenant', 'houseOwner')->paginate(10));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('sanctum_tenant')->check()) {
            return response()->json(['message' => 'Unauthorized: Only tenants can create payments.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'house_id' => 'required|exists:houses,id',
                'house_owner_id' => 'required|exists:house_owners,id',
                'details' => 'nullable|string',
                'date_payment' => 'required|string|max:10',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        $tenant = Auth::guard('sanctum_tenant')->user();
        $validatedData['user_email'] = $tenant->email_address;

        $payment = Payment::create($validatedData);

        return response()->json([
            'message' => 'Payment recorded successfully',
            'payment' => $payment->load('house', 'houseOwner', 'tenant')
        ], 201);
    }

    public function show(Payment $payment)
    {
        if (
            (Auth::guard('sanctum_tenant')->check() && Auth::guard('sanctum_tenant')->user()->email_address === $payment->user_email) ||
            (Auth::guard('sanctum_house_owner')->check() && Auth::guard('sanctum_house_owner')->user()->id === $payment->house_owner_id) ||
            Auth::guard('sanctum_administrator')->check()
        ) {
            return response()->json($payment->load('house', 'houseOwner', 'tenant'));
        }

        return response()->json(['message' => 'Unauthorized to view this payment.'], 403);
    }

    public function update(Request $request, Payment $payment)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can update payments.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'house_id' => 'sometimes|exists:houses,id',
                'house_owner_id' => 'sometimes|exists:house_owners,id',
                'user_email' => 'sometimes|email|exists:tenants,email_address',
                'details' => 'nullable|string',
                'date_payment' => 'sometimes|string|max:10',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        $payment->update($validatedData);

        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment->load('house', 'houseOwner', 'tenant')
        ]);
    }

    public function destroy(Payment $payment)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can delete payments.'], 403);
        }

        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted successfully'
        ], 200);
    }
}
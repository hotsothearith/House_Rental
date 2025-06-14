<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\HouseOwner; // Ensure this is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class HouseController extends Controller
{
    /**
     * Display a listing of the houses.
     * Accessible by anyone.
     */
    public function index(Request $request)
    {
        $houses = House::query();

        // Basic filtering matching ERD fields
        if ($request->has('house_city')) {
            $houses->where('house_city', 'like', '%' . $request->house_city . '%');
        }
        if ($request->has('house_district')) {
            $houses->where('house_district', 'like', '%' . $request->house_district . '%');
        }
        if ($request->has('house_state')) {
            $houses->where('house_state', 'like', '%' . $request->house_state . '%');
        }
        if ($request->has('min_price')) {
            $houses->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $houses->where('price', '<=', $request->max_price);
        }
        if ($request->has('rooms')) {
            $houses->where('rooms', $request->rooms);
        }
        if ($request->has('house_type')) {
            $houses->where('house_type', $request->house_type);
        }
        // Add more filters as needed

        return response()->json($houses->paginate(10));
    }

    /**
     * Store a newly created house in storage.
     * Requires authentication and user must be a House Owner.
     */
public function store(Request $request)
{
    if (!Auth::guard('sanctum_house_owner')->check()) {
        return response()->json(['message' => 'Unauthorized: Only house owners can post houses.'], 403);
    }

    try {
        $validated = $request->validate([
            'address' => 'required|string|max:100',
            'house_city' => 'required|string|max:20',
            'house_district' => 'required|string|max:20',
            'house_state' => 'required|string|max:20',
            'descriptions' => 'nullable|string',
            'price' => 'required|integer',
            'house_type' => 'required|string|max:20',
            'rooms' => 'required|integer',
            'furnitures' => 'nullable|string|max:30',
            'variation' => 'nullable|string|max:30',
            'image' => 'nullable|string|max:120',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation Error',
            'errors' => $e->errors()
        ], 422);
    }

    $houseOwner = Auth::guard('sanctum_house_owner')->user();
    $validated['house_owner_id'] = $houseOwner->id;

    $house = \App\Models\House::create($validated);

    return response()->json([
        'message' => 'House created successfully',
        'house' => $house
    ], 201);
}

    /**
     * Display the specified house.
     * Accessible by anyone.
     */
    public function show(House $house)
    {
        return response()->json($house->load('houseOwner')); // Eager load houseOwner
    }

    /**
     * Update the specified house in storage.
     * Requires authentication and user must be the owning House Owner.
     */
    public function update(Request $request, House $house)
    {
        if (!Auth::guard('sanctum_house_owner')->check() || Auth::guard('sanctum_house_owner')->user()->id !== $house->house_owner_id) {
            return response()->json(['message' => 'Unauthorized: You do not own this house or are not a house owner.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'address' => 'sometimes|string|max:100',
                'house_city' => 'sometimes|string|max:20',
                'house_district' => 'sometimes|string|max:20',
                'house_state' => 'sometimes|string|max:20',
                'descriptions' => 'nullable|string',
                'price' => 'sometimes|integer|min:0',
                'house_type' => 'sometimes|string|max:20',
                'rooms' => 'sometimes|integer|min:1',
                'furnitures' => 'nullable|string|max:30',
                'variation' => 'nullable|string|max:30',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // For a single main image
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($house->image && Storage::disk('public')->exists(str_replace(Storage::url(''), '', $house->image))) {
                Storage::disk('public')->delete(str_replace(Storage::url(''), '', $house->image));
            }
            $path = $request->file('image')->store('house_images', 'public');
            $validatedData['image'] = Storage::url($path);
        }

        $house->update($validatedData);

        return response()->json([
            'message' => 'House updated successfully',
            'house' => $house
        ]);
    }

    /**
     * Remove the specified house from storage.
     * Requires authentication and user must be the owning House Owner.
     */
    public function destroy(House $house)
    {
        if (!Auth::guard('sanctum_house_owner')->check() || Auth::guard('sanctum_house_owner')->user()->id !== $house->house_owner_id) {
            return response()->json(['message' => 'Unauthorized: You do not own this house or are not a house owner.'], 403);
        }

        // Delete associated image from storage
        if ($house->image && Storage::disk('public')->exists(str_replace(Storage::url(''), '', $house->image))) {
            Storage::disk('public')->delete(str_replace(Storage::url(''), '', $house->image));
        }

        $house->delete();

        return response()->json([
            'message' => 'House deleted successfully'
        ], 200);
    }

    /**
     * Display a listing of all houses for admin.
     * Requires authentication and user must be an Administrator.
     */
    public function indexAdmin(Request $request)
    {
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can access this resource.'], 403);
        }

        return response()->json(
            House::with('houseOwner')->paginate(10)
        );
    }
}

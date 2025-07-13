<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\HouseOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\HouseResource; // Ensure this is imported
// Make sure to import HouseOwner as you're using it, or remove if not used in this specific controller.

class HouseController extends Controller
{
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

        // Use the HouseResource to transform the paginated results
        return HouseResource::collection($houses->paginate(10));
    }

    public function store(Request $request)
    {
        // Use Sanctum's current authenticated user for authorization and owner ID
        $houseOwner = Auth::guard('sanctum_house_owner')->user();

        if (!$houseOwner) {
            return response()->json(['message' => 'Unauthorized: Only house owners can post houses.'], 403);
        }

        try {
            $validated = $request->validate([
                'address' => 'required|string|max:100',
                'house_city' => 'required|string|max:20',
                'house_district' => 'required|string|max:20',
                'house_state' => 'required|string|max:20',
                'descriptions' => 'nullable|string', // Keep 'descriptions' if that's your DB column name
                'price' => 'required|integer',
                'house_type' => 'required|string|max:20',
                'rooms' => 'required|integer',
                'furnitures' => 'nullable|string|max:30',
                'variation' => 'nullable|string|max:30',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                // 'house_owner_id' is automatically set, no need for validation here if using authenticated user
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        // Set house_owner_id from the authenticated user
        $validated['house_owner_id'] = $houseOwner->id;

        if ($request->hasFile('image')) {
     $path = $request->file('image')->store('house_images', 'public');
    $validated['image'] = $path;
 }

 $house = House::create($validated);

    // Use HouseResource to transform the created house, including image_url
    $house->image_url = $house->image ? asset('storage/' . $house->image) : null;

 return response()->json($house);
    }

    public function show(House $house)
    {
        // Use HouseResource to transform a single house, including image_url
        return new HouseResource($house->load('houseOwner')); // Eager load houseOwner and transform
    }

    public function update(Request $request, House $house)
    {
        $houseOwner = Auth::guard('sanctum_house_owner')->user();

        if (!$houseOwner || $houseOwner->id !== $house->house_owner_id) {
            return response()->json(['message' => 'Unauthorized: You do not own this house or are not a house owner.'], 403);
        }

        try {
            $validatedData = $request->validate([
                'address' => 'sometimes|string|max:100',
                'house_city' => 'sometimes|string|max:20',
                'house_district' => 'sometimes|string|max:20',
                'house_state' => 'sometimes|string|max:20',
                'descriptions' => 'nullable|string', // Keep 'descriptions' if that's your DB column name
                'price' => 'sometimes|integer|min:0',
                'house_type' => 'sometimes|string|max:20',
                'rooms' => 'sometimes|integer|min:1',
                'furnitures' => 'nullable|string|max:30',
                'variation' => 'nullable|string|max:30',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        // THIS IS THE CORRECTED IMAGE PROCESSING LOGIC for UPDATE
        if ($request->hasFile('image')) {
            // Delete old image if it exists. Make sure $house->image is the relative path from DB.
            if ($house->image && Storage::disk('public')->exists($house->image)) {
                Storage::disk('public')->delete($house->image);
            }
            $path = $request->file('image')->store('house_images', 'public');
            $validatedData['image'] = $path; // <--- ONLY STORE THE RELATIVE PATH IN THE DB
        } elseif (array_key_exists('image', $validatedData) && $validatedData['image'] === null) {
            // If the frontend explicitly sent 'image: null' (meaning remove existing image)
            if ($house->image && Storage::disk('public')->exists($house->image)) {
                Storage::disk('public')->delete($house->image);
            }
            $validatedData['image'] = null; // Set image field to null in DB
        } else {
            // If image field was not sent in request, do not touch existing image
            unset($validatedData['image']);
        }


        $house->update($validatedData);

        // Return the updated house using the resource
        return new HouseResource($house->load('houseOwner')); // Load owner if needed for response
    }

    public function destroyAdmin($id)
    {
        // Authorization check for administrator (consider using middleware for this)
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can delete houses.'], 403);
        }

        $house = House::find($id);
        if (!$house) {
            return response()->json(['message' => 'House not found.'], 404);
        }

        // Delete associated image file
        if ($house->image && Storage::disk('public')->exists($house->image)) {
            Storage::disk('public')->delete($house->image);
        }

        $house->delete();

        return response()->json(['message' => 'House deleted successfully.'], 200);
    }

    public function indexAdmin(Request $request)
    {
        // Authorization check for administrator
        if (!Auth::guard('sanctum_administrator')->check()) {
            return response()->json(['message' => 'Unauthorized: Only administrators can access this resource.'], 403);
        }

        // Use HouseResource collection for admin index as well
        return HouseResource::collection(House::with('houseOwner')->paginate(10));
    }
}
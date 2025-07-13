<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class HouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'house_city' => $this->house_city,
            'house_district' => $this->house_district,
            'house_state' => $this->house_state,
            'description' => $this->descriptions, // <--- IMPORTANT: Ensure this matches your DB column.
                                                // If your DB column is 'descriptions', kee            // If it's 'description', change to $this->description.
            'price' => $this->price,
            'house_type' => $this->house_type,
            'rooms' => $this->rooms,
            'furnitures' => $this->furnitures,
            'variation' => $this->variation,
            'image' => $this->image, // Still include the relative path for completeness if needed
            'image_url' => $this->image ? asset('storage/' . $this->image) : null, // <--- THIS IS WHAT generates the full URL
            'house_owner_id' => $this->house_owner_id,
            'houseOwner' => new UserResource($this->whenLoaded('houseOwner')), // Assuming you have a UserResource
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

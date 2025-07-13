<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     public function toArray($request)
    {
       return [
            'id' => $this->id,
            'user_id' => $this->user_id,         // Assuming you have this column or relationship
            'house_id' => $this->house_id,       // Assuming you have this column or relationship
            'message' => $this->message,
            'rating' => $this->rating ?? null,   // Assuming 'rating' column, make nullable if not always present
            // You might want to include related data, e.g., the name of the user who left feedback
            'tenant_full_name' => $this->whenLoaded('tenant', function () { // If feedback belongsTo tenant
                return $this->tenant->full_name ?? null;
            }),
            'house_address' => $this->whenLoaded('house', function () { // If feedback belongsTo house
                return $this->house->address ?? null;
            }),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}

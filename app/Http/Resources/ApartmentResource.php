<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray($request): array
    {
        $user = User::findOrFail($this->owner_id);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'description' => $this->description,
            'rate' => $this->rate !== null ? round((float) $this->rate, 1) : null,
            'price' => $this->price,
            'city' => $this->city,
            'area' => $this->area,
            'rooms' => $this->rooms,
            'bathrooms' => $this->bathrooms,
            'status' => $this->status,
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => asset('storage/' . $image->apartment_image_path)
                ];
            }),
            'owner' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'profile_photo' => asset('storage/' . $user->profile_photo)
            ],
        ];
    }
}

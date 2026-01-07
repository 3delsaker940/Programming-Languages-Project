<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray($request): array
    {
        $owner = User::findOrFail($this->owner_id);
        $user = $request->user();
        $is_favorite = $user->favoritesApartment()->where('apartment_id', $this->id)->exists();
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
            'is_favorite' => $is_favorite,
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => asset('storage/' . $image->apartment_image_path)
                ];
            }),
            'owner' => [
                'id' => $owner->id,
                'first_name' => $owner->first_name,
                'last_name' => $owner->last_name,
                'profile_photo' => asset('storage/' . $owner->profile_photo)
            ],
        ];
    }
}

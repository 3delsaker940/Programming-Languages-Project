<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'city' => $this->city,
            'area' => $this->area,
            'rooms' => $this->rooms,
            'bathrooms' => $this->bathrooms,
            'status' => $this->status,
            'images' => $this->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => url('storage/'.$image->apartment_image_path)
                ];
            }),
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'min_rooms' => 'sometimes|min:1|nullable',
            'max_rooms' => 'sometimes|max:20',
            'min_bathrooms' => 'sometimes|min:1|nullable',
            'max_bathrooms' => 'sometimes|max:20',
            'min_area' => 'sometimes|min:0',
            'max_area' => 'sometimes|max:6000',
            'min_price' => 'sometimes|min:0',
            'max_price' => 'sometimes|max:1000000',
            'city' => 'string|sometimes|nullable'
        ];
    }
}

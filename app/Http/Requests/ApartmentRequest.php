<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => ['required', Rule::in(['apartment', 'house', 'villa', 'otherwise'])],
            'price' => 'required|numeric',
            'rooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'city' => ['required', Rule::in([
                'damascus',
                'rif_dimashq',
                'aleppo',
                'homs',
                'hama',
                'latakia',
                'tartus',
                'idlib',
                'deir_ez_zor',
                'raqqa',
                'al_hasakah',
                'daraa',
                'as_suwayda',
                'quneitra',
            ])],
            'area' => 'required|numeric|min:0',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,heic|max:16384'
        ];
    }
}

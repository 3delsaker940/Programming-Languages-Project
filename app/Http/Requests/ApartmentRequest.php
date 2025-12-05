<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric',
            'rooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'city' => 'required|string|max:255',
            'images' => 'required|array|min:1',        
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,heic|max:16384'
        ];
    }
}

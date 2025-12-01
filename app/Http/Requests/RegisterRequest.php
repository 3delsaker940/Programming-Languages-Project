<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'first_name' => 'required|string|between:2,20',
            'last_name' => 'required|string|between:2,20',
            'birthdate' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'after_or_equal:' . now()->subYears(100)->format('Y-m-d'),
            ],

            'number' => [
                'required',
                'unique:users,number',
                'string',
                'regex:/^(?:\+9639|09|009639)\d{8}$/'
            ],
            'status' => 'string|sometimes|in:pending,active,rejected,frozen',
            'type' => 'sometimes|in:tenant,owner',
            'id_photo_back' => 'required|image|max:16384|mimes:jpg,jpeg,png,heic',
            'id_photo_front' => 'required|image|max:16384|mimes:jpg,jpeg,png,heic',
            'profile_photo' => 'required|image|max:16384|mimes:jpg,jpeg,png,heic'
        ];
    }
}

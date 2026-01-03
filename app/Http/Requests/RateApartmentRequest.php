<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RateApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rate' => 'required|between:0.5,5|',
            'apartment_id' => 'required|exists:apartments,id|integer',
            'comment' => 'sometimes|string|nullable'
        ];
    }
}

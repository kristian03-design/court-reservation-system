<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'court_name' => ['required', 'string', 'max:255'],
            'court_type' => ['required', 'in:Basketball,Volleyball,Badminton,Tennis,Futsal'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'hourly_rate' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:available,maintenance,closed'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:80'],
        ];
    }
}

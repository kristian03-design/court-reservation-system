<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() ?? false;
    }

    public function rules(): array
    {
        return [
            'proof_image' => ['required', 'image', 'max:4096'],
            'reference_number' => ['required', 'string', 'max:100'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() ?? false;
    }

    public function rules(): array
    {
        return [
            'court_id' => ['required', 'exists:courts,id'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'players' => ['required', 'integer', 'min:1', 'max:100'],
            'payment_method' => ['required', 'in:paymongo,gcash_upload,pay_at_venue'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends AppointmentDetailsRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'booking_token' => ['required', 'uuid', Rule::in(array_keys($this->session()->get('appointment.tokens', [])))],
            'treatment_id' => ['required', 'integer', Rule::exists('treatments', 'id')->where('is_active', true)],
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i:sP'],
        ];
    }
}

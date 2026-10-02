<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:120'],
            'email' => ['bail', 'required', 'string', 'email', 'max:254'],

            'phone' => [
                'bail',
                'required',
                'string',
                'max:30',
                'phone',
            ],

            'phone_country' => [
                'bail',
                'required',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
            ],

            'birth_date' => [
                'bail',
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:' . now(
                    config('appointments.timezone')
                )->toDateString(),
            ],

            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'other']),
            ],

            'reason' => [
                'nullable',
                Rule::in(['general', 'pain', 'cosmetic', 'other']),
            ],

            'notes' => ['nullable', 'string', 'max:5000'],
            'is_new_patient' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('appointment.validation.required'),
            'string' => __('appointment.validation.string'),
            'email' => __('appointment.validation.email'),
            'max' => __('appointment.validation.max'),
            'in' => __('appointment.validation.choice'),
            'boolean' => __('appointment.validation.choice'),

            'phone.phone' => __('appointment.validation.phone'),

            'phone_country.required' => __('appointment.validation.country'),
            'phone_country.string' => __('appointment.validation.country'),
            'phone_country.size' => __('appointment.validation.country'),
            'phone_country.regex' => __('appointment.validation.country'),

            'birth_date.date_format' => __('appointment.validation.birth_date'),
            'birth_date.before_or_equal' => __('appointment.validation.birth_date'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('appointment.fields.name'),
            'email' => __('appointment.fields.email'),
            'phone' => __('appointment.fields.phone'),
            'birth_date' => __('appointment.fields.birth_date'),
            'gender' => __('appointment.fields.gender'),
            'reason' => __('appointment.fields.reason'),
            'notes' => __('appointment.fields.notes'),
            'is_new_patient' => __('appointment.fields.new_patient'),
        ];
    }
}

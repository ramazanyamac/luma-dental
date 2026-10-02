<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
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
                'required_with:phone',
                'nullable',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
            ],
            'subject' => [
                'bail',
                'required',
                'string',
                Rule::in(['general', 'appointment', 'treatment', 'other']),
            ],
            'message' => ['bail', 'required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('contact.validation.required'),
            'string' => __('contact.validation.string'),
            'email' => __('contact.validation.email'),
            'max' => __('contact.validation.max'),
            'in' => __('contact.validation.in'),
            'phone.phone' => __('contact.validation.phone'),
            'phone_country.required_with' => __('contact.validation.phone_country'),
            'phone_country.string' => __('contact.validation.phone_country'),
            'phone_country.size' => __('contact.validation.phone_country'),
            'phone_country.regex' => __('contact.validation.phone_country'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('contact.form.name'),
            'email' => __('contact.form.email'),
            'phone' => __('contact.form.phone'),
            'subject' => __('contact.validation.subject'),
            'message' => __('contact.form.message'),
        ];
    }
}

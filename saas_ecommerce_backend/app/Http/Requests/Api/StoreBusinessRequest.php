<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'desired_url_name' => ['required', 'string', 'max:120', 'alpha_dash', 'unique:businesses,desired_url_name'],
            'business_location' => ['required', 'string', 'max:255'],
            'business_registration_number' => ['required', 'string', 'max:120', 'unique:businesses,business_registration_number'],
            'business_email' => ['nullable', 'string', 'email', 'max:255'],
            'business_phone' => ['required', 'string', 'max:20'],
        ];
    }
}

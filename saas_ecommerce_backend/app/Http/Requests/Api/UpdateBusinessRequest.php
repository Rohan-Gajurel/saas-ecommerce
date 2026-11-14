<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessRequest extends FormRequest
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
        $businessId = $this->route('business')?->id;

        return [
            'business_name' => ['required', 'string', 'max:255'],
            'desired_url_name' => ['required', 'string', 'max:120', 'alpha_dash', Rule::unique('businesses', 'desired_url_name')->ignore($businessId)],
            'business_location' => ['required', 'string', 'max:255'],
            'business_registration_number' => ['required', 'string', 'max:120', Rule::unique('businesses', 'business_registration_number')->ignore($businessId)],
            'business_email' => ['nullable', 'string', 'email', 'max:255'],
            'business_phone' => ['required', 'string', 'max:20'],
        ];
    }
}

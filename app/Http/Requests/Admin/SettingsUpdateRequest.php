<?php

namespace App\Http\Requests\Admin;

/**
 * Settings Update Request
 * 
 * Validates application settings update.
 * 
 * @package SchoolHub\Http\Requests\Admin
 */
class SettingsUpdateRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Auth::check() && \Auth::user()->hasRole('super_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'school_name' => 'required|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'school_email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'academic_session' => 'required|string|max:50',
            'current_term' => 'required|in:first_term,second_term,third_term,annual',
            'timezone' => 'required|timezone',
            'currency' => 'required|string|max:10',
            'school_motto' => 'nullable|string|max:255',
            // `color` is not a valid Laravel rule; validate hex colors
            // (#RRGGBB or #RGB) with an explicit regex instead.
            'primary_color' => ['nullable', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', 'max:7'],
            'secondary_color' => ['nullable', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', 'max:7'],
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl,none',
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'school_name.required' => 'School name is required.',
            'school_logo.image' => 'School logo must be an image.',
            'school_email.email' => 'School email must be a valid email address.',
            'academic_session.required' => 'Academic session is required.',
            'current_term.required' => 'Current term is required.',
            'timezone.required' => 'Timezone is required.',
            'currency.required' => 'Currency is required.',
            'primary_color.regex' => 'Primary color must be a valid hexadecimal color (e.g. #4f46e5).',
            'secondary_color.regex' => 'Secondary color must be a valid hexadecimal color (e.g. #0ea5e9).',
        ];
    }
}

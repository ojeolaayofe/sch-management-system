<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Teacher Store Request
 *
 * Validates teacher creation input.
 * Email is validated against the users table for uniqueness.
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class TeacherStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('super_admin') ||
               auth()->user()->hasRole('school_administrator');
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'teacher_id'   => 'nullable|string|max:50|unique:teachers,teacher_id',
            'first_name'   => 'required|string|max:100',
            'middle_name'  => 'nullable|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'nullable|email|max:255|unique:users,email',
            'phone'        => 'nullable|string|max:50',
            'address'      => 'nullable|string',
            'qualification'=> 'nullable|string|max:100',
            'employment_date' => 'nullable|date',
            'status'       => 'required|in:active,inactive,on_leave,resigned',
            'password'     => 'nullable|string|min:8|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required'  => 'First name is required.',
            'last_name.required'   => 'Last name is required.',
            'status.required'      => 'Status is required.',
            'email.email'          => 'Please enter a valid email address.',
            'email.unique'         => 'This email is already registered.',
            'password.min'         => 'Password must be at least 8 characters.',
            'password.confirmed'   => 'Password confirmation does not match.',
        ];
    }
}

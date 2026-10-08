<?php

namespace App\Http\Requests\Admin;

use App\Models\Teacher;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Teacher Update Request
 *
 * Validates teacher update input.
 * Email uniqueness ignores the current teacher's associated user.
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class TeacherUpdateRequest extends FormRequest
{
    /**
     * The teacher instance being updated.
     */
    public ?Teacher $teacher = null;

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
        $this->teacher = Teacher::find($this->route('teacher') ?? $this->route('id'));

        // The user_id to ignore when checking email uniqueness
        $ignoreUserId = $this->teacher?->user_id ?? 0;

        return [
            'teacher_id'      => 'nullable|string|max:50|unique:teachers,teacher_id,' . ($this->teacher?->id ?? 0),
            'first_name'      => 'required|string|max:100',
            'middle_name'     => 'nullable|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'nullable|email|max:255|unique:users,email,' . $ignoreUserId,
            'phone'           => 'nullable|string|max:50',
            'address'         => 'nullable|string',
            'qualification'   => 'nullable|string|max:100',
            'employment_date' => 'nullable|date',
            'status'          => 'required|in:active,inactive,on_leave,resigned',
            'password'        => 'nullable|string|min:8|confirmed',
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

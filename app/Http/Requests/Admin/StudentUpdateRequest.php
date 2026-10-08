<?php

namespace App\Http\Requests\Admin;

/**
 * Student Update Request
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class StudentUpdateRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        return \Auth::user()->hasRole('super_admin') || \Auth::user()->hasRole('school_administrator');
    }

    public function rules(): array
    {
        $studentId = $this->route('student') ?? $this->route('id');
        return [
            'student_id' => 'nullable|string|max:50|unique:students,student_id,' . $studentId,
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'passport_photo' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'admission_date' => 'required|date',
            'status' => 'required|in:active,inactive,transferred,graduated',
            'parent_guardian_name' => 'nullable|string|max:100',
            'parent_guardian_phone' => 'nullable|string|max:50',
            'parent_guardian_email' => 'nullable|email|max:100',
            'parent_guardian_relationship' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'admission_date.required' => 'Admission date is required.',
            'status.required' => 'Status is required.',
            'student_id.unique' => 'Student ID already exists.',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

/**
 * Teacher Subject Assignment Update Request
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class TeacherSubjectAssignmentUpdateRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        return \Auth::user()->hasRole('super_admin') || \Auth::user()->hasRole('school_administrator');
    }

    public function rules(): array
    {
        return [
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_arm_id' => 'required|exists:class_arms,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'status' => 'required|in:active,inactive',
        ];
    }
}

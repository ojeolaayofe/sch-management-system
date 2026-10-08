<?php

namespace App\Http\Requests\Teacher;

use App\Services\AttendanceService;

/**
 * Teacher Attendance Store Request
 *
 * Validates bulk attendance submission. Authorization (teacher profile +
 * class assignment) is enforced server-side in the controller/service.
 *
 * @package SchoolHub\Http\Requests\Teacher
 */
class AttendanceStoreRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->teacher !== null;
    }

    public function rules(): array
    {
        return [
            'class_arm_id' => 'required|integer|exists:class_arms,id',
            'attendance_date' => 'required|date|before_or_equal:today',
            'statuses' => 'required|array',
            'statuses.*' => 'required|string|in:present,absent,late,excused',
        ];
    }

    public function messages(): array
    {
        return [
            'statuses.required' => 'Please mark at least one student.',
            'statuses.*.in' => 'Each student must be marked with a valid status (present, absent, late or excused).',
            'attendance_date.before_or_equal:today' => 'Attendance date cannot be in the future.',
        ];
    }
}

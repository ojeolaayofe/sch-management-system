<?php

namespace App\Http\Requests\Admin;

/**
 * Academic Term Update Request
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class AcademicTermUpdateRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        return \Auth::user()->hasRole('super_admin') || \Auth::user()->hasRole('school_administrator');
    }

    public function rules(): array
    {
        return [
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'name' => 'required|in:first_term,second_term,third_term,annual',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
            'status' => 'required|in:active,inactive',
        ];
    }
}

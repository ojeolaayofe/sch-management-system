<?php

namespace App\Http\Requests\Admin;

/**
 * Academic Session Update Request
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class AcademicSessionUpdateRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        return \Auth::user()->hasRole('super_admin') || \Auth::user()->hasRole('school_administrator');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:academic_sessions,name,' . $this->route('academic_session'),
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
            'status' => 'required|in:active,inactive',
        ];
    }
}

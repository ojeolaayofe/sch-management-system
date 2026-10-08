<?php

namespace App\Http\Requests\Admin;

/**
 * Academic Session Store Request
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class AcademicSessionStoreRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        return \Auth::user()->hasRole('super_admin') || \Auth::user()->hasRole('school_administrator');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:academic_sessions,name',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
            'status' => 'required|in:active,inactive',
        ];
    }
}

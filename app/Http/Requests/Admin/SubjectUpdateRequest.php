<?php

namespace App\Http\Requests\Admin;

/**
 * Subject Update Request
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class SubjectUpdateRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        return \Auth::user()->hasRole('super_admin') || \Auth::user()->hasRole('school_administrator');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code,' . $this->route('subject'),
            'category' => 'required|in:core,elective,science,arts,commercial,technical',
            'status' => 'required|in:active,inactive',
        ];
    }
}

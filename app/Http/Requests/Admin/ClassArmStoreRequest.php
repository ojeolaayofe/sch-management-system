<?php

namespace App\Http\Requests\Admin;

/**
 * Class Arm Store Request
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class ClassArmStoreRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        return \Auth::user()->hasRole('super_admin') || \Auth::user()->hasRole('school_administrator');
    }

    public function rules(): array
    {
        return [
            'class_id' => 'required|exists:classes,id',
            'arm_name' => 'required|string|max:10',
            'capacity' => 'integer|min:1',
            'class_teacher_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ];
    }
}

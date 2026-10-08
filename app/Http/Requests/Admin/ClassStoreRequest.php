<?php

namespace App\Http\Requests\Admin;

/**
 * Class Store Request
 *
 * @package SchoolHub\Http\Requests\Admin
 */
class ClassStoreRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        return \Auth::user()->hasRole('super_admin') || \Auth::user()->hasRole('school_administrator');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:classes,code',
            'section' => 'required|in:nursery,primary,junior_secondary,senior_secondary',
            'display_order' => 'integer|min:0',
            'status' => 'required|in:active,inactive',
        ];
    }
}

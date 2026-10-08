<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TeacherStoreRequest;
use App\Http\Requests\Admin\TeacherUpdateRequest;
use App\Models\Teacher;
use App\Services\TeacherService;

/**
 * Admin Teacher Controller
 *
 * Provides full CRUD for teacher management.
 * Uses TeacherService for business logic.
 * Authorizes via TeacherPolicy.
 */
class TeacherController extends \App\Http\Controllers\Controller
{
    private TeacherService $service;

    public function __construct(TeacherService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of teachers.
     */
    public function index()
    {
        $this->authorize(Teacher::class, 'viewAny');

        $teachers = $this->service->getAll(
            perPage: 15,
            search: request('search', ''),
            status: request('status', 'all')
        );

        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        $this->authorize(Teacher::class, 'create');

        $teacherId = Teacher::generateTeacherId();

        return view('admin.teachers.create', compact('teacherId'));
    }

    /**
     * Store a newly created teacher.
     */
    public function store(TeacherStoreRequest $request)
    {
        $this->authorize(Teacher::class, 'create');

        try {
            $this->service->create($request->validated());

            \Session::flash('success', 'Teacher created successfully.');
            return redirect()->route('admin.teachers.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to create teacher: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified teacher.
     */
    public function show(int $id)
    {
        $teacher = $this->service->findWithUser($id);

        $this->authorize('view', $teacher);

        return view('admin.teachers.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(int $id)
    {
        $teacher = Teacher::findOrFail($id);

        $this->authorize('update', $teacher);

        return view('admin.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher.
     */
    public function update(TeacherUpdateRequest $request, int $id)
    {
        $teacher = Teacher::findOrFail($id);

        $this->authorize('update', $teacher);

        try {
            $validated = $request->validated();

            if (!empty($validated['password'])) {
                $this->service->updatePassword($teacher, $validated['password']);
                unset($validated['password'], $validated['password_confirmation']);
            }

            $this->service->update($teacher, $validated);

            \Session::flash('success', 'Teacher updated successfully.');
            return redirect()->route('admin.teachers.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to update teacher: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(int $id)
    {
        $teacher = Teacher::findOrFail($id);

        $this->authorize('delete', $teacher);

        try {
            $this->service->delete($teacher);

            \Session::flash('success', 'Teacher deleted successfully.');
            return redirect()->route('admin.teachers.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to delete teacher: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}

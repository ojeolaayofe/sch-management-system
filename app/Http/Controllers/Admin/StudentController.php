<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StudentStoreRequest;
use App\Http\Requests\Admin\StudentUpdateRequest;
use App\Services\StudentService;
use App\Models\Student;

/**
 * Student Controller
 *
 * @package SchoolHub\Http\Controllers\Admin
 */
class StudentController extends \App\Http\Controllers\Controller
{
    private StudentService $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of students
     */
    public function index()
    {
        $students = $this->service->getAll(
            perPage: 15,
            search: request('search', ''),
            status: request('status', 'all'),
            classId: request('class_id', 0),
            sessionId: request('session_id', 0)
        );

        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $studentId = Student::generateStudentId();
        return view('admin.students.create', compact('studentId'));
    }

    /**
     * Store a newly created student
     */
    public function store(StudentStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            \Session::flash('success', 'Student created successfully.');
            return redirect()->route('admin.students.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to create student: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified student
     */
    public function show(int $id)
    {
        $student = $this->service->findWithRelations($id);
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(int $id)
    {
        $student = Student::findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student
     */
    public function update(StudentUpdateRequest $request, int $id)
    {
        try {
            $student = Student::findOrFail($id);
            $this->service->update($student, $request->validated());
            
            \Session::flash('success', 'Student updated successfully.');
            return redirect()->route('admin.students.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to update student: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy(int $id)
    {
        try {
            $student = Student::findOrFail($id);
            $this->service->delete($student);
            
            \Session::flash('success', 'Student deleted successfully.');
            return redirect()->route('admin.students.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to delete student: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}

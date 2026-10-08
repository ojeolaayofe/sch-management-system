<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TeacherSubjectAssignmentStoreRequest;
use App\Http\Requests\Admin\TeacherSubjectAssignmentUpdateRequest;
use App\Services\TeacherSubjectAssignmentService;

/**
 * Teacher Subject Assignment Controller
 *
 * @package SchoolHub\Http\Controllers\Admin
 */
class TeacherSubjectAssignmentController extends \App\Http\Controllers\Controller
{
    private TeacherSubjectAssignmentService $service;

    public function __construct(TeacherSubjectAssignmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $assignments = $this->service->getAll(
            perPage: 15,
            search: request('search', ''),

            status: request('status', 'all'),
            teacherId: request('teacher_id', 0),
            subjectId: request('subject_id', 0)
        );

        $teachers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'teacher');
        })->pluck('name', 'id');

        $subjects = \App\Models\Subject::pluck('name', 'id');

        return view('admin.teacher-subject-assignments.index', compact('assignments', 'teachers', 'subjects'));
    }

    public function create()
    {
        $teachers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'teacher');
        })->pluck('name', 'id');

        $subjects = \App\Models\Subject::pluck('name', 'id');

        $classArms = \App\Models\ClassArm::with('class')->get()->mapToGroups(function($arm) {
            return [$arm->class->name => $arm];
        });

        $sessions = \App\Models\AcademicSession::pluck('name', 'id');
        
        return view('admin.teacher-subject-assignments.create', compact('teachers', 'subjects', 'classArms', 'sessions'));
    }

    public function store(TeacherSubjectAssignmentStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            \Session::flash('success', 'Teacher subject assignment created successfully.');
            return redirect()->route('admin.teacher-subject-assignments.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to create teacher subject assignment: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit(int $id)
    {
        $assignment = \App\Models\TeacherSubjectAssignment::findOrFail($id);

        $teachers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'teacher');
        })->pluck('name', 'id');

        $subjects = \App\Models\Subject::pluck('name', 'id');

        $classArms = \App\Models\ClassArm::with('class')->get()->mapToGroups(function($arm) {
            return [$arm->class->name => $arm];
        });

        $sessions = \App\Models\AcademicSession::pluck('name', 'id');
        
        return view('admin.teacher-subject-assignments.edit', compact('assignment', 'teachers', 'subjects', 'classArms', 'sessions'));
    }

    public function update(TeacherSubjectAssignmentUpdateRequest $request, int $id)
    {
        try {
            $assignment = \App\Models\TeacherSubjectAssignment::findOrFail($id);
            $this->service->update($assignment, $request->validated());
            
            \Session::flash('success', 'Teacher subject assignment updated successfully.');
            return redirect()->route('admin.teacher-subject-assignments.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to update teacher subject assignment: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy(int $id)
    {
        try {
            $assignment = \App\Models\TeacherSubjectAssignment::findOrFail($id);
            $this->service->delete($assignment);
            
            \Session::flash('success', 'Teacher subject assignment deleted successfully.');
            return redirect()->route('admin.teacher-subject-assignments.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to delete teacher subject assignment: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function export()
    {
        $assignments = \App\Models\TeacherSubjectAssignment::with(['teacher', 'subject', 'classArm.class', 'academicSession'])->get();
        
        $filename = 'teacher-subject-assignments-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Teacher', 'Subject', 'Class', 'Arm', 'Session', 'Status']);
        
        foreach ($assignments as $assignment) {
            fputcsv($output, [
                $assignment->id,
                $assignment->teacher->name ?? '',
                $assignment->subject->name ?? '',
                $assignment->classArm->class->name ?? '',
                $assignment->classArm->arm_name ?? '',
                $assignment->academicSession->name ?? '',
                $assignment->status,
            ]);
        }
        
        fclose($output);
        exit;
    }
}

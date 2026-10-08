<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ClassArmStoreRequest;
use App\Http\Requests\Admin\ClassArmUpdateRequest;
use App\Services\ClassArmService;

/**
 * Class Arm Controller
 *
 * @package SchoolHub\Http\Controllers\Admin
 */
class ClassArmController extends \App\Http\Controllers\Controller
{
    private ClassArmService $service;

    public function __construct(ClassArmService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $arms = $this->service->getAll(
            perPage: 15,
            search: request('search', ''),

            status: request('status', 'all'),
            classId: request('class_id', 0)
        );

        $classes = \App\Models\ClassModel::pluck('name', 'id');

        return view('admin.class-arms.index', compact('arms', 'classes'));
    }

    public function create()
    {
        $classes = \App\Models\ClassModel::pluck('name', 'id');
        $teachers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'teacher');
        })->pluck('name', 'id');
        
        return view('admin.class-arms.create', compact('classes', 'teachers'));
    }

    public function store(ClassArmStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            \Session::flash('success', 'Class arm created successfully.');
            return redirect()->route('admin.class-arms.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to create class arm: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit(int $id)
    {
        $arm = \App\Models\ClassArm::findOrFail($id);
        $classes = \App\Models\ClassModel::pluck('name', 'id');
        $teachers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'teacher');
        })->pluck('name', 'id');
        
        return view('admin.class-arms.edit', compact('arm', 'classes', 'teachers'));
    }

    public function update(ClassArmUpdateRequest $request, int $id)
    {
        try {
            $arm = \App\Models\ClassArm::findOrFail($id);
            $this->service->update($arm, $request->validated());
            
            \Session::flash('success', 'Class arm updated successfully.');
            return redirect()->route('admin.class-arms.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to update class arm: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy(int $id)
    {
        try {
            $arm = \App\Models\ClassArm::findOrFail($id);
            $this->service->delete($arm);
            
            \Session::flash('success', 'Class arm deleted successfully.');
            return redirect()->route('admin.class-arms.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to delete class arm: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function export()
    {
        $arms = \App\Models\ClassArm::with(['class', 'classTeacher'])->get();
        
        $filename = 'class-arms-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Class', 'Arm Name', 'Capacity', 'Class Teacher', 'Status']);
        
        foreach ($arms as $arm) {
            fputcsv($output, [
                $arm->id,
                $arm->class->name ?? '',
                $arm->arm_name,
                $arm->capacity,
                $arm->classTeacher->name ?? '',
                $arm->status,
            ]);
        }
        
        fclose($output);
        exit;
    }
}

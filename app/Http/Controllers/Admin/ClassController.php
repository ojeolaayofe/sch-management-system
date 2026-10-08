<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ClassStoreRequest;
use App\Http\Requests\Admin\ClassUpdateRequest;
use App\Services\ClassService;

/**
 * Class Controller
 *
 * @package SchoolHub\Http\Controllers\Admin
 */
class ClassController extends \App\Http\Controllers\Controller
{
    private ClassService $service;

    public function __construct(ClassService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $classes = $this->service->getAll(
            perPage: 15,
            search: request('search', ''),

            status: request('status', 'all'),
            section: request('section', 'all')
        );

        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(ClassStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            \Session::flash('success', 'Class created successfully.');
            return redirect()->route('admin.classes.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to create class: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit(int $id)
    {
        $class = \App\Models\ClassModel::findOrFail($id);
        
        return view('admin.classes.edit', compact('class'));
    }

    public function update(ClassUpdateRequest $request, int $id)
    {
        try {
            $class = \App\Models\ClassModel::findOrFail($id);
            $this->service->update($class, $request->validated());
            
            \Session::flash('success', 'Class updated successfully.');
            return redirect()->route('admin.classes.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to update class: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy(int $id)
    {
        try {
            $class = \App\Models\ClassModel::findOrFail($id);
            $this->service->delete($class);
            
            \Session::flash('success', 'Class deleted successfully.');
            return redirect()->route('admin.classes.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to delete class: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function export()
    {
        $classes = \App\Models\ClassModel::all();
        
        $filename = 'classes-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Code', 'Section', 'Display Order', 'Status']);
        
        foreach ($classes as $class) {
            fputcsv($output, [
                $class->id,
                $class->name,
                $class->code,
                $class->section,
                $class->display_order,
                $class->status,
            ]);
        }
        
        fclose($output);
        exit;
    }
}

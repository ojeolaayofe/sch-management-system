<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SubjectStoreRequest;
use App\Http\Requests\Admin\SubjectUpdateRequest;
use App\Services\SubjectService;

/**
 * Subject Controller
 *
 * @package SchoolHub\Http\Controllers\Admin
 */
class SubjectController extends \App\Http\Controllers\Controller
{
    private SubjectService $service;

    public function __construct(SubjectService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $subjects = $this->service->getAll(
            perPage: 15,
            search: request('search', ''),

            status: request('status', 'all'),
            category: request('category', 'all')
        );

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(SubjectStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            \Session::flash('success', 'Subject created successfully.');
            return redirect()->route('admin.subjects.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to create subject: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit(int $id)
    {
        $subject = \App\Models\Subject::findOrFail($id);
        
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(SubjectUpdateRequest $request, int $id)
    {
        try {
            $subject = \App\Models\Subject::findOrFail($id);
            $this->service->update($subject, $request->validated());
            
            \Session::flash('success', 'Subject updated successfully.');
            return redirect()->route('admin.subjects.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to update subject: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy(int $id)
    {
        try {
            $subject = \App\Models\Subject::findOrFail($id);
            $this->service->delete($subject);
            
            \Session::flash('success', 'Subject deleted successfully.');
            return redirect()->route('admin.subjects.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to delete subject: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function export()
    {
        $subjects = \App\Models\Subject::all();
        
        $filename = 'subjects-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Code', 'Category', 'Status']);
        
        foreach ($subjects as $subject) {
            fputcsv($output, [
                $subject->id,
                $subject->name,
                $subject->code,
                $subject->category,
                $subject->status,
            ]);
        }
        
        fclose($output);
        exit;
    }
}

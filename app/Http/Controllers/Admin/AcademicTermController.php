<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AcademicTermStoreRequest;
use App\Http\Requests\Admin\AcademicTermUpdateRequest;
use App\Services\AcademicTermService;

/**
 * Academic Term Controller
 *
 * @package SchoolHub\Http\Controllers\Admin
 */
class AcademicTermController extends \App\Http\Controllers\Controller
{
    private AcademicTermService $service;

    public function __construct(AcademicTermService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $terms = $this->service->getAll(
            perPage: 15,
            search: request('search', ''),

            status: request('status', 'all'),
            sessionId: request('session_id', 0)
        );

        $sessions = \App\Models\AcademicSession::pluck('name', 'id');

        return view('admin.academic-terms.index', compact('terms', 'sessions'));
    }

    public function create()
    {
        $sessions = \App\Models\AcademicSession::pluck('name', 'id');
        
        return view('admin.academic-terms.create', compact('sessions'));
    }

    public function store(AcademicTermStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            \Session::flash('success', 'Academic term created successfully.');
            return redirect()->route('admin.academic-terms.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to create academic term: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit(int $id)
    {
        $term = \App\Models\AcademicTerm::findOrFail($id);
        $sessions = \App\Models\AcademicSession::pluck('name', 'id');
        
        return view('admin.academic-terms.edit', compact('term', 'sessions'));
    }

    public function update(AcademicTermUpdateRequest $request, int $id)
    {
        try {
            $term = \App\Models\AcademicTerm::findOrFail($id);
            $this->service->update($term, $request->validated());
            
            \Session::flash('success', 'Academic term updated successfully.');
            return redirect()->route('admin.academic-terms.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to update academic term: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy(int $id)
    {
        try {
            $term = \App\Models\AcademicTerm::findOrFail($id);
            $this->service->delete($term);
            
            \Session::flash('success', 'Academic term deleted successfully.');
            return redirect()->route('admin.academic-terms.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to delete academic term: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function setCurrent(int $id)
    {
        try {
            $this->service->setCurrent($id);
            
            \Session::flash('success', 'Current term set successfully.');
            return redirect()->route('admin.academic-terms.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to set current term: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function export()
    {
        $terms = \App\Models\AcademicTerm::with('academicSession')->get();
        
        $filename = 'academic-terms-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Session', 'Name', 'Start Date', 'End Date', 'Is Current', 'Status']);
        
        foreach ($terms as $term) {
            fputcsv($output, [
                $term->id,
                $term->academicSession->name ?? '',
                $term->name,
                $term->start_date->format('Y-m-d'),
                $term->end_date->format('Y-m-d'),
                $term->is_current ? 'Yes' : 'No',
                $term->status,
            ]);
        }
        
        fclose($output);
        exit;
    }
}

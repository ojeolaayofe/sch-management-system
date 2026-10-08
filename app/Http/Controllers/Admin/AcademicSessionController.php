<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AcademicSessionStoreRequest;
use App\Http\Requests\Admin\AcademicSessionUpdateRequest;
use App\Services\AcademicSessionService;

/**
 * Academic Session Controller
 *
 * @package SchoolHub\Http\Controllers\Admin
 */
class AcademicSessionController extends \App\Http\Controllers\Controller
{
    private AcademicSessionService $service;

    public function __construct(AcademicSessionService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sessions = $this->service->getAll(
            perPage: 15,
            search: request('search', ''),

            status: request('status', 'all')
        );

        return view('admin.academic-sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.academic-sessions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicSessionStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            \Session::flash('success', 'Academic session created successfully.');
            return redirect()->route('admin.academic-sessions.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to create academic session: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $session = \App\Models\AcademicSession::with('terms')->findOrFail($id);
        
        return view('admin.academic-sessions.show', compact('session'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $session = \App\Models\AcademicSession::findOrFail($id);
        
        return view('admin.academic-sessions.edit', compact('session'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicSessionUpdateRequest $request, int $id)
    {
        try {
            $session = \App\Models\AcademicSession::findOrFail($id);
            $this->service->update($session, $request->validated());
            
            \Session::flash('success', 'Academic session updated successfully.');
            return redirect()->route('admin.academic-sessions.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to update academic session: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $session = \App\Models\AcademicSession::findOrFail($id);
            $this->service->delete($session);
            
            \Session::flash('success', 'Academic session deleted successfully.');
            return redirect()->route('admin.academic-sessions.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to delete academic session: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Set current session
     */
    public function setCurrent(int $id)
    {
        try {
            $this->service->setCurrent($id);
            
            \Session::flash('success', 'Current session set successfully.');
            return redirect()->route('admin.academic-sessions.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to set current session: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Export to CSV
     */
    public function export()
    {
        $sessions = \App\Models\AcademicSession::all();
        
        $filename = 'academic-sessions-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Start Date', 'End Date', 'Is Current', 'Status']);
        
        foreach ($sessions as $session) {
            fputcsv($output, [
                $session->id,
                $session->name,
                $session->start_date->format('Y-m-d'),
                $session->end_date->format('Y-m-d'),
                $session->is_current ? 'Yes' : 'No',
                $session->status,
            ]);
        }
        
        fclose($output);
        exit;
    }
}

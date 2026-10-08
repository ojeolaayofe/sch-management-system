<?php

namespace App\Http\Controllers\Admin;

/**
 * Application Settings Controller
 * 
 * @package SchoolHub\Http\Controllers\Admin
 */
class SettingsController extends \App\Http\Controllers\Controller
{
    /**
     * Settings Service
     */
    private \App\Services\SettingsService $settingsService;
    
    /**
     * Constructor
     * 
     * @param \App\Services\SettingsService $settingsService
     */
    public function __construct(\App\Services\SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }
    
    /**
     * Display settings page
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $settings = $this->settingsService->getAll();
        
        return view('admin.settings.index', compact('settings'));
    }
    
    /**
     * Update settings
     * 
     * @param \App\Http\Requests\Admin\SettingsUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(\App\Http\Requests\Admin\SettingsUpdateRequest $request)
    {
        try {
            $this->settingsService->update($request->validated());
            
            \Session::flash('success', 'Settings updated successfully.');
            
            return redirect()->route('admin.settings.index');
        } catch (\Exception $e) {
            \Session::flash('error', 'Failed to update settings: ' . $e->getMessage());
            
            return redirect()->route('admin.settings.index');
        }
    }
}

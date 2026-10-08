<?php

namespace App\Http\Controllers\Admin;

/**
 * Admin Dashboard Controller
 * 
 * @package SchoolHub\Http\Controllers\Admin
 */
class DashboardController extends \App\Http\Controllers\Controller
{
    /**
     * Display the admin dashboard
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('admin.dashboard.index');
    }
}

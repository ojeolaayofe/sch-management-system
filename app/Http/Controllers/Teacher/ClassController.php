<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherDashboardService;

/**
 * Teacher My Classes Controller
 *
 * Shows only classes assigned to the authenticated teacher.
 *
 * @package SchoolHub\Http\Controllers\Teacher
 */
class ClassController extends Controller
{
    public function __construct(private TeacherDashboardService $service)
    {
    }

    /**
     * Display the teacher's assigned classes.
     */
    public function index()
    {
        $userId = auth()->id();

        abort_unless(auth()->user()->teacher, 403, 'No teacher profile found for this account.');

        $classes = $this->service->getMyClasses($userId);

        return view('teacher.classes.index', compact('classes'));
    }
}

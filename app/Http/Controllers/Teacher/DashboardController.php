<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherDashboardService;

/**
 * Teacher Dashboard Controller
 *
 * @package SchoolHub\Http\Controllers\Teacher
 */
class DashboardController extends Controller
{
    public function __construct(private TeacherDashboardService $service)
    {
    }

    /**
     * Display the teacher dashboard.
     */
    public function index()
    {
        $userId = auth()->id();

        // Ensure the user has a teacher profile
        $teacher = auth()->user()->teacher;

        abort_unless($teacher, 403, 'No teacher profile found for this account.');

        $stats = $this->service->getDashboardStats($userId);

        return view('teacher.dashboard.index', [
            'teacher' => $teacher,
            'stats' => $stats,
        ]);
    }
}

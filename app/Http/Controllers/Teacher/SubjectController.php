<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherDashboardService;

/**
 * Teacher My Subjects Controller
 *
 * Shows only subjects assigned to the authenticated teacher.
 *
 * @package SchoolHub\Http\Controllers\Teacher
 */
class SubjectController extends Controller
{
    public function __construct(private TeacherDashboardService $service)
    {
    }

    /**
     * Display the teacher's assigned subjects.
     */
    public function index()
    {
        $userId = auth()->id();

        abort_unless(auth()->user()->teacher, 403, 'No teacher profile found for this account.');

        $subjects = $this->service->getMySubjects($userId);

        return view('teacher.subjects.index', compact('subjects'));
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\AttendanceStoreRequest;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

/**
 * Teacher Attendance Controller
 *
 * Lets a teacher mark attendance for classes they are assigned to.
 * All class access is verified server-side against the teacher's active
 * subject assignments; no browser-supplied identifier is trusted.
 *
 * @package SchoolHub\Http\Controllers\Teacher
 */
class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $service)
    {
    }

    /**
     * Attendance entry page.
     *
     * Optional query params: class_arm_id (defaults to first assigned arm),
     * date (defaults to today).
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        abort_unless(auth()->user()->teacher, 403, 'No teacher profile found for this account.');

        $assignedArms = $this->service->getAssignedClassArms($userId);

        $classArmId = $request->input('class_arm_id');
        $classArm = null;

        if ($classArmId) {
            // Aborts 403 if not assigned, 404 if unknown.
            $classArm = $this->service->authorizedClassArm($userId, (int) $classArmId);
        } else {
            $classArm = $assignedArms->first();
        }

        $date = $request->input('date') ?: now()->toDateString();
        \Illuminate\Support\Facades\Validator::make(
            ['date' => $date],
            ['date' => 'required|date|before_or_equal:today']
        )->validate();

        $students = $existing = collect();
        $session = \App\Models\AcademicSession::getCurrent();

        if ($classArm) {
            $students = $this->service->getStudentsInClassArm($classArm->id);
            $existing = $this->service->getAttendanceForDate($classArm->id, $date);
        }

        return view('teacher.attendance.index', compact('assignedArms', 'classArm', 'students', 'existing', 'date', 'session'));
    }

    /**
     * Save attendance for the selected class and date.
     */
    public function store(AttendanceStoreRequest $request)
    {
        $teacher = auth()->user()->teacher;

        abort_unless($teacher, 403, 'No teacher profile found for this account.');

        $result = $this->service->saveAttendance(
            $teacher->id,
            $request->integer('class_arm_id'),
            $request->input('attendance_date'),
            $request->input('statuses', [])
        );

        if (!empty($result['errors'])) {
            \Session::flash('error', implode(' ', $result['errors']));
            return redirect()->back()->withInput();
        }

        $message = $result['saved'] > 0
            ? "Attendance saved successfully ({$result['saved']} student" . ($result['saved'] === 1 ? '' : 's') . ' marked).'
            : 'Attendance saved. No valid students were marked.';

        \Session::flash('success', $message);

        return redirect()
            ->route('teacher.attendance.index', [
                'class_arm_id' => $request->integer('class_arm_id'),
                'date' => $request->input('attendance_date'),
            ]);
    }
}

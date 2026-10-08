<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\ResultManagementService;
use App\Services\StudentResultService;
use Illuminate\Http\Request;

/**
 * Admin Results Management Controller
 *
 * Central Results Management page: an administrator selects a session, term,
 * class and class arm, and sees every student in that arm with their score
 * summary. Individual results can be viewed and downloaded as PDFs.
 *
 * Authorization: restricted to super_admin and school_administrator by the
 * `role:super_admin|school_administrator` route middleware; the controller
 * re-checks the roles defensively (mirroring StudentResultController).
 *
 * @package SchoolHub\Http\Controllers\Admin
 */
class ResultsController extends Controller
{
    public function __construct(private ResultManagementService $service)
    {
    }

    /**
     * Ensure only super_admin / school_administrator can use this controller.
     *
     * NOTE: must not be named `authorize()` — that would collide with
     * AuthorizesRequests::authorize() and break the entire controller.
     */
    public function ensureAuthorized(Request $request): void
    {
        abort_unless(
            $request->user()->hasRole('super_admin') || $request->user()->hasRole('school_administrator'),
            403,
            'You are not authorized to access Results Management.'
        );
    }

    /**
     * Results Management index: session/term/class/arm selector + student list.
     */
    public function index(Request $request)
    {
        $this->ensureAuthorized($request);

        $sessionId = (int) $request->input('academic_session_id', 0);
        $termId = $request->filled('academic_term_id') ? (int) $request->input('academic_term_id') : null;
        $classId = (int) $request->input('class_id', 0);
        $armId = (int) $request->input('class_arm_id', 0);

        $rows = ($sessionId > 0 && $armId > 0)
            ? $this->service->studentsForArm($sessionId, $termId, $classId, $armId)
            : [];

        $viewData = [
            'sessions' => $this->service->sessions(),
            'terms' => $sessionId > 0 ? $this->service->termsForSession($sessionId) : [],
            'classes' => $this->service->classes(),
            'arms' => $classId > 0 ? $this->service->armsForClass($classId) : [],
            'rows' => $rows,
            'selected' => [
                'academic_session_id' => $sessionId ?: null,
                'academic_term_id' => $termId,
                'class_id' => $classId ?: null,
                'class_arm_id' => $armId ?: null,
            ],
        ];

        return view('admin.results.index', $viewData);
    }

    /**
     * View a single student's full result within the selected context.
     * Reuses StudentResultService::buildResult (same logic as the PDF).
     */
    public function show(Request $request, int $student)
    {
        $this->ensureAuthorized($request);

        $validated = $request->validate([
            'academic_session_id' => 'required|integer|exists:academic_sessions,id',
            'academic_term_id' => 'nullable|integer|exists:academic_terms,id',
        ]);

        $studentModel = Student::findOrFail($student);
        $sessionId = (int) $validated['academic_session_id'];
        $termId = isset($validated['academic_term_id']) && $validated['academic_term_id'] !== ''
            ? (int) $validated['academic_term_id']
            : null;

        $result = $this->service->buildResult($studentModel, $sessionId, $termId);

        return view('admin.results.show', [
            'result' => $result,
            'student' => $studentModel,
            'sessions' => $this->service->sessions(),
            'termsForSession' => $sessionId
                ? collect($this->service->termsForSession($sessionId))->pluck('label', 'id')->all()
                : [],
            'academic_session_id' => $sessionId,
            'academic_term_id' => $termId,
        ]);
    }

    /**
     * Stream the individual result PDF. Delegates to the existing
     * StudentResultController so the PDF implementation stays in one place
     * (single source of truth for the individual result PDF).
     */
    public function pdf(Request $request, int $student)
    {
        $this->ensureAuthorized($request);

        return app(StudentResultController::class)->pdf($request, $student);
    }
}

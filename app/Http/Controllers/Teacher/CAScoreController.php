<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Services\ScoreEntryService;
use Illuminate\Http\Request;

/**
 * Teacher CA Score Entry Controller
 *
 * @package SchoolHub\Http\Controllers\Teacher
 */
class CAScoreController extends Controller
{
    public function __construct(private ScoreEntryService $service)
    {
    }

    /**
     * Show the CA score entry form (select context).
     */
    public function index()
    {
        $userId = auth()->id();
        $teacher = $this->service->getTeacher($userId);
        abort_unless($teacher, 403, 'No teacher profile found.');

        $assignments = $this->service->getTeacherAssignments($userId);
        $assessmentTypes = $this->service->getAssessmentTypes();
        $sessions = AcademicSession::where('status', 'active')->orderByDesc('start_date')->get();
        $currentSession = AcademicSession::getCurrent();

        // Group assignments by subject for the selection UI
        $assignmentsBySubject = $assignments->groupBy('subject_id');

        return view('teacher.ca-scores.index', compact(
            'teacher', 'assignments', 'assignmentsBySubject', 'assessmentTypes', 'sessions', 'currentSession'
        ));
    }

    /**
     * Show the student score grid for a selected context.
     */
    public function create(Request $request)
    {
        $userId = auth()->id();
        $teacher = $this->service->getTeacher($userId);
        abort_unless($teacher, 403, 'No teacher profile found.');

        $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_arm_id' => 'required|integer|exists:class_arms,id',
            'assessment_type_id' => 'required|integer|exists:assessment_types,id',
            'academic_session_id' => 'required|integer|exists:academic_sessions,id',
        ]);

        $subjectId = $request->input('subject_id');
        $classArmId = $request->input('class_arm_id');
        $assessmentTypeId = $request->input('assessment_type_id');
        $sessionId = $request->input('academic_session_id');
        $termId = $request->input('academic_term_id') ? (int) $request->input('academic_term_id') : null;

        // SECURITY: Teacher must be assigned to this subject + class arm
        abort_unless(
            $this->service->isAssignedTo($userId, $subjectId, $classArmId),
            403,
            'You are not assigned to this subject/class.'
        );

        $classArm = \App\Models\ClassArm::with('class')->findOrFail($classArmId);
        $subject = \App\Models\Subject::findOrFail($subjectId);
        $assessmentType = \App\Models\AssessmentType::findOrFail($assessmentTypeId);
        $session = AcademicSession::findOrFail($sessionId);
        $terms = $this->service->getTermsForSession($sessionId);
        $students = $this->service->getStudentsInClassArm($classArmId);
        $existingScores = $this->service->getCAScores($classArmId, $subjectId, $assessmentTypeId, $sessionId, $termId);

        return view('teacher.ca-scores.create', compact(
            'teacher', 'classArm', 'subject', 'assessmentType', 'session',
            'terms', 'students', 'existingScores'
        ));
    }

    /**
     * Save CA scores.
     */
    public function store(Request $request)
    {
        $userId = auth()->id();
        $teacher = $this->service->getTeacher($userId);
        abort_unless($teacher, 403, 'No teacher profile found.');

        $validated = $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_arm_id' => 'required|integer|exists:class_arms,id',
            'assessment_type_id' => 'required|integer|exists:assessment_types,id',
            'academic_session_id' => 'required|integer|exists:academic_sessions,id',
            'academic_term_id' => 'nullable|integer|exists:academic_terms,id',
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0',
        ]);

        $subjectId = $validated['subject_id'];
        $classArmId = $validated['class_arm_id'];
        $assessmentTypeId = $validated['assessment_type_id'];
        $sessionId = $validated['academic_session_id'];
        $termId = $validated['academic_term_id'] ?? null;

        // SECURITY: Verify assignment
        abort_unless(
            $this->service->isAssignedTo($userId, $subjectId, $classArmId),
            403,
            'You are not assigned to this subject/class.'
        );

        $classArm = \App\Models\ClassArm::findOrFail($classArmId);
        $classId = $classArm->class_id;

        $result = $this->service->saveCAScores(
            $teacher->id,
            $classArmId,
            $classId,
            $subjectId,
            $assessmentTypeId,
            $sessionId,
            $termId,
            $validated['scores']
        );

        if (!empty($result['errors'])) {
            return back()
                ->withInput()
                ->withErrors(['scores' => $result['errors']]);
        }

        return redirect()
            ->route('teacher.ca-scores.index')
            ->with('success', "{$result['saved']} CA score(s) saved successfully.");
    }
}

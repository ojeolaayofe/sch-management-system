<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\Attendance;
use App\Models\ClassArm;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherSubjectAssignment;
use Illuminate\Support\Carbon;

/**
 * Attendance Service
 *
 * Handles teacher attendance marking. All queries are scoped to the
 * authenticated teacher's active subject assignments. Teachers can only
 * mark attendance for class arms they are assigned to.
 *
 * @package SchoolHub\Services
 */
class AttendanceService
{
    /**
     * Allowed attendance statuses (matches the attendance.status enum).
     *
     * @var array<int, string>
     */
    public const STATUSES = ['present', 'absent', 'late', 'excused'];

    /**
     * Get the Teacher model for the authenticated user.
     */
    public function getTeacher(int $userId): ?Teacher
    {
        return Teacher::where('user_id', $userId)->first();
    }

    /**
     * Get the class arms this teacher is actively assigned to.
     *
     * Returns a collection of ClassArm models, one per distinct assigned arm.
     */
    public function getAssignedClassArms(int $userId)
    {
        return TeacherSubjectAssignment::where('teacher_id', $userId)
            ->where('status', 'active')
            ->with('classArm.class')
            ->get()
            ->map(fn ($assignment) => $assignment->classArm)
            ->filter()
            ->unique('id')
            ->values();
    }

    /**
     * Check whether the teacher (by user id) is actively assigned to the class arm.
     */
    public function isAssignedToClassArm(int $userId, int $classArmId): bool
    {
        return TeacherSubjectAssignment::where('teacher_id', $userId)
            ->where('status', 'active')
            ->where('class_arm_id', $classArmId)
            ->exists();
    }

    /**
     * Load a class arm, or abort 403/404 if the teacher is not assigned to it.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizedClassArm(int $userId, int $classArmId): ClassArm
    {
        $classArm = ClassArm::with('class')->find($classArmId);

        abort_unless($classArm, 404, 'Class not found.');
        abort_unless($this->isAssignedToClassArm($userId, $classArmId), 403, 'You are not assigned to this class.');

        return $classArm;
    }

    /**
     * Get all active students currently assigned to the given class arm.
     */
    public function getStudentsInClassArm(int $classArmId)
    {
        return Student::whereHas('classAssignments', function ($query) use ($classArmId) {
            $query->where('class_arm_id', $classArmId)
                ->where('status', 'active');
        })
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Get existing attendance statuses for a class arm and date.
     *
     * @return \Illuminate\Support\Collection<int, string> student_id => status
     */
    public function getAttendanceForDate(int $classArmId, string $date)
    {
        return Attendance::where('class_arm_id', $classArmId)
            ->whereDate('attendance_date', $date)
            ->pluck('status', 'student_id');
    }

    /**
     * Determine the academic session and term that apply to a date (best effort).
     *
     * @return array{session: AcademicSession|null, term: AcademicTerm|null}
     */
    public function contextForDate(string $date): array
    {
        $day = Carbon::parse($date);

        $session = AcademicSession::where('start_date', '<=', $day)
            ->where('end_date', '>=', $day)
            ->orderBy('start_date')
            ->first()
            ?? AcademicSession::getCurrent()
            ?? AcademicSession::orderBy('start_date')->last();

        $term = null;

        if ($session) {
            $term = AcademicTerm::where('academic_session_id', $session->id)
                ->where('start_date', '<=', $day)
                ->where('end_date', '>=', $day)
                ->orderBy('start_date')
                ->first();
        }

        return ['session' => $session, 'term' => $term];
    }

    /**
     * Save (upsert) attendance for an entire class arm on a date.
     *
     * Server-side rules enforced here:
     * - The teacher must be actively assigned to the class arm.
     * - Only students actively assigned to the class arm may be marked.
     * - Only allowed statuses are accepted.
     * - Existing records for the student/class/date are updated, never duplicated
     *   (also guaranteed by the unique_student_class_date index).
     *
     * @param array $statuses Map of student_id => status
     * @return array{saved: int, skipped: int, errors: array<int, string>}
     */
    public function saveAttendance(
        int $teacherModelId,
        int $classArmId,
        string $date,
        array $statuses
    ): array {
        $classArm = $this->authorizedClassArmForTeacher($teacherModelId, $classArmId);
        $context = $this->contextForDate($date);
        $activeStudentIds = $this->getStudentsInClassArm($classArmId)->pluck('id')->all();

        $saved = 0;
        $skipped = 0;
        $errors = [];

        foreach ($statuses as $studentId => $status) {
            $studentId = (int) $studentId;
            $status = strtolower(trim((string) $status));

            if (!in_array($status, self::STATUSES, true)) {
                $errors[] = "Student {$studentId}: invalid attendance status.";
                continue;
            }

            if (!in_array($studentId, $activeStudentIds, true)) {
                $skipped++;
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_id' => $classArm->class_id,
                    'class_arm_id' => $classArmId,
                    'attendance_date' => $date,
                ],
                [
                    'teacher_id' => $teacherModelId,
                    'academic_session_id' => $context['session']?->id,
                    'academic_term_id' => $context['term']?->id,
                    'status' => $status,
                ]
            );

            $saved++;
        }

        return ['saved' => $saved, 'skipped' => $skipped, 'errors' => $errors];
    }

    /**
     * Load a class arm after verifying the teacher model (teachers.id) is
     * actively assigned to it.
     */
    private function authorizedClassArmForTeacher(int $teacherModelId, int $classArmId): ClassArm
    {
        // teacher_subject_assignments.teacher_id references users.id,
        // while attendance.teacher_id references teachers.id.
        $userId = Teacher::where('id', $teacherModelId)->value('user_id');

        abort_unless($userId, 403, 'No teacher profile found.');
        abort_unless($this->isAssignedToClassArm((int) $userId, $classArmId), 403, 'You are not assigned to this class.');

        return ClassArm::with('class')->findOrFail($classArmId);
    }
}

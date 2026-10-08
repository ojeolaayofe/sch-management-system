<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\AssessmentScore;
use App\Models\AssessmentType;
use App\Models\ClassArm;
use App\Models\ExaminationScore;
use App\Models\StudentClassAssignment;
use App\Models\Teacher;
use App\Models\TeacherSubjectAssignment;

/**
 * Score Entry Service
 *
 * Handles CA and Examination score entry for teachers.
 * All queries are scoped to the authenticated teacher's assignments.
 *
 * @package SchoolHub\Services
 */
class ScoreEntryService
{
    /**
     * Get the Teacher model for the authenticated user.
     */
    public function getTeacher(int $userId): ?Teacher
    {
        return Teacher::where('user_id', $userId)->first();
    }

    /**
     * Get the teacher's active assignments with full relationships.
     */
    public function getTeacherAssignments(int $userId)
    {
        return TeacherSubjectAssignment::where('teacher_id', $userId)
            ->where('status', 'active')
            ->with(['subject', 'classArm.class', 'academicSession'])
            ->get();
    }

    /**
     * Check if the teacher is assigned to a specific subject + class arm combination.
     */
    public function isAssignedTo(int $userId, int $subjectId, int $classArmId): bool
    {
        return TeacherSubjectAssignment::where('teacher_id', $userId)
            ->where('status', 'active')
            ->where('subject_id', $subjectId)
            ->where('class_arm_id', $classArmId)
            ->exists();
    }

    /**
     * Get students in a class arm.
     */
    public function getStudentsInClassArm(int $classArmId)
    {
        return StudentClassAssignment::where('class_arm_id', $classArmId)
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->map(fn ($assignment) => $assignment->student)
            ->filter()
            ->values();
    }

    /**
     * Get existing CA scores for a specific context.
     */
    public function getCAScores(
        int $classArmId,
        int $subjectId,
        int $assessmentTypeId,
        int $sessionId,
        ?int $termId
    ) {
        return AssessmentScore::where('class_arm_id', $classArmId)
            ->where('subject_id', $subjectId)
            ->where('assessment_type_id', $assessmentTypeId)
            ->where('academic_session_id', $sessionId)
            ->when($termId, fn ($q) => $q->where('academic_term_id', $termId), fn ($q) => $q->whereNull('academic_term_id'))
            ->pluck('score', 'student_id');
    }

    /**
     * Save CA scores (upsert).
     *
     * @param array $scores Map of student_id => score
     */
    public function saveCAScores(
        int $teacherModelId,
        int $classArmId,
        int $classId,
        int $subjectId,
        int $assessmentTypeId,
        int $sessionId,
        ?int $termId,
        array $scores
    ): array {
        $assessmentType = AssessmentType::findOrFail($assessmentTypeId);
        $maxScore = $assessmentType->max_score;
        $saved = 0;
        $errors = [];

        foreach ($scores as $studentId => $score) {
            if ($score === '' || $score === null) {
                continue;
            }

            $score = (float) $score;

            if ($score < 0) {
                $errors[] = "Student {$studentId}: score cannot be negative";
                continue;
            }

            if ($score > $maxScore) {
                $errors[] = "Student {$studentId}: score exceeds max ({$maxScore})";
                continue;
            }

            // Verify student belongs to this class arm
            $studentAssignment = StudentClassAssignment::where('student_id', $studentId)
                ->where('class_arm_id', $classArmId)
                ->where('status', 'active')
                ->first();

            if (!$studentAssignment) {
                $errors[] = "Student {$studentId} is not in the selected class";
                continue;
            }

            // Upsert
            AssessmentScore::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'assessment_type_id' => $assessmentTypeId,
                    'academic_session_id' => $sessionId,
                    'academic_term_id' => $termId,
                ],
                [
                    'teacher_id' => $teacherModelId,
                    'class_id' => $classId,
                    'class_arm_id' => $classArmId,
                    'score' => $score,
                    'max_score' => $maxScore,
                ]
            );

            $saved++;
        }

        return ['saved' => $saved, 'errors' => $errors];
    }

    /**
     * Get existing exam scores for a specific context.
     */
    public function getExamScores(
        int $classArmId,
        int $subjectId,
        int $sessionId,
        ?int $termId
    ) {
        return ExaminationScore::where('class_arm_id', $classArmId)
            ->where('subject_id', $subjectId)
            ->where('academic_session_id', $sessionId)
            ->when($termId, fn ($q) => $q->where('academic_term_id', $termId), fn ($q) => $q->whereNull('academic_term_id'))
            ->pluck('score', 'student_id');
    }

    /**
     * Save exam scores (upsert).
     *
     * @param array $scores Map of student_id => score
     */
    public function saveExamScores(
        int $teacherModelId,
        int $classArmId,
        int $classId,
        int $subjectId,
        int $sessionId,
        ?int $termId,
        array $scores,
        float $maxScore = 100.0
    ): array {
        $saved = 0;
        $errors = [];

        foreach ($scores as $studentId => $score) {
            if ($score === '' || $score === null) {
                continue;
            }

            $score = (float) $score;

            if ($score < 0) {
                $errors[] = "Student {$studentId}: score cannot be negative";
                continue;
            }

            if ($score > $maxScore) {
                $errors[] = "Student {$studentId}: score exceeds max ({$maxScore})";
                continue;
            }

            // Verify student belongs to this class arm
            $studentAssignment = StudentClassAssignment::where('student_id', $studentId)
                ->where('class_arm_id', $classArmId)
                ->where('status', 'active')
                ->first();

            if (!$studentAssignment) {
                $errors[] = "Student {$studentId} is not in the selected class";
                continue;
            }

            // Upsert
            ExaminationScore::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'academic_session_id' => $sessionId,
                    'academic_term_id' => $termId,
                ],
                [
                    'teacher_id' => $teacherModelId,
                    'class_id' => $classId,
                    'class_arm_id' => $classArmId,
                    'score' => $score,
                    'max_score' => $maxScore,
                ]
            );

            $saved++;
        }

        return ['saved' => $saved, 'errors' => $errors];
    }

    /**
     * Get all active assessment types.
     */
    public function getAssessmentTypes()
    {
        return AssessmentType::where('is_active', true)
            ->orderBy('display_order')
            ->get();
    }

    /**
     * Get terms for a session.
     */
    public function getTermsForSession(int $sessionId)
    {
        return AcademicTerm::where('academic_session_id', $sessionId)
            ->where('status', 'active')
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Get the teacher's class arms grouped by subject.
     * Returns: [subject_id => [assignments...]]
     */
    public function getTeacherClassArmsBySubject(int $userId)
    {
        return $this->getTeacherAssignments($userId)
            ->groupBy('subject_id');
    }
}

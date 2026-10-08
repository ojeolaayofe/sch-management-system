<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\AssessmentScore;
use App\Models\AssessmentType;
use App\Models\Attendance;
use App\Models\ClassArm;
use App\Models\ExaminationScore;
use App\Models\GradingScale;
use App\Models\ResultRemark;
use App\Models\Student;
use App\Models\StudentClassAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Student Result Service
 *
 * Builds a complete academic result for a single student within a
 * session/term/class context. Pure read-only aggregation — it never
 * writes scores. All scores are taken as recorded by the teacher
 * score-entry features (CA per assessment type + examination).
 *
 * @package SchoolHub\Services
 */
class StudentResultService
{
    /**
     * Build a full result for a student.
     *
     * @param Student $student
     * @param int $sessionId
     * @param int|null $termId
     * @return array
     */
    public function buildResult(Student $student, int $sessionId, ?int $termId): array
    {
        $session = AcademicSession::findOrFail($sessionId);
        $term = $termId ? AcademicTerm::findOrFail($termId) : null;

        $assignment = $this->currentAssignment($student, $sessionId, $termId);
        $classArm = $assignment?->classArm;
        $class = $assignment?->class ?? null;

        // Subjects: the union of every subject that has a CA or exam score
        // recorded for this student in this context. This reflects exactly
        // what the teachers have entered, without inventing subjects.
        $subjectIds = $this->scoredSubjectIds($student, $sessionId, $termId);

        $subjects = collect();
        $totalScore = 0.0;
        $totalMax = 0.0;
        $subjectCount = 0;

        foreach ($subjectIds as $subjectId) {
            $subject = \App\Models\Subject::find($subjectId);
            if (!$subject) {
                continue;
            }

            $row = $this->subjectResult($student, $subjectId, $sessionId, $termId, $classArm);

            $subjects->push($row);
            $totalScore += (float) $row['total'];
            $totalMax += (float) $row['total_max'];
            $subjectCount++;
        }

        // Overall percentage / grade
        $overallPercentage = $totalMax > 0 ? ($totalScore / $totalMax) * 100 : null;
        $overallGrade = $overallPercentage !== null ? $this->gradeFor((float) $overallPercentage) : null;

        $remark = $termId
            ? ResultRemark::where('student_id', $student->id)
                ->where('academic_session_id', $sessionId)
                ->where('academic_term_id', $termId)
                ->first()
            : null;

        return [
            'student' => $student,
            'session' => $session,
            'term' => $term,
            'class' => $class,
            'classArm' => $classArm,
            'subjects' => $subjects->values(),
            'totals' => [
                'score' => round($totalScore, 2),
                'max' => round($totalMax, 2),
                'percentage' => $overallPercentage !== null ? round($overallPercentage, 2) : null,
                'grade' => $overallGrade['grade'] ?? null,
                'remark' => $overallGrade['remark'] ?? null,
            ],
            'attendance' => $this->attendanceSummary($student, $sessionId, $termId),
            'remark' => $remark,
            'generated_at' => now(),
        ];
    }

    /**
     * Compact, list-oriented summary of a student's result for the given
     * session/term. Delegates all score aggregation and grading to
     * buildResult() so the Results Management index uses the exact same
     * calculation logic as the individual result PDF.
     *
     * @return array{subject_count: int, total_score: float|null, total_max: float|null, percentage: float|null, grade: ?string, remark: ?string}
     */
    public function summaryFor(Student $student, int $sessionId, ?int $termId): array
    {
        $result = $this->buildResult($student, $sessionId, $termId);
        $totals = $result['totals'];
        $subjectCount = $result['subjects']->count();

        return [
            'subject_count' => $subjectCount,
            'total_score' => $subjectCount > 0 ? $totals['score'] : null,
            'total_max' => $subjectCount > 0 ? $totals['max'] : null,
            'percentage' => $subjectCount > 0 ? $totals['percentage'] : null,
            'grade' => $totals['grade'] ?? null,
            'remark' => $totals['remark'] ?? null,
        ];
    }

    /**
     * Human-readable result status derived from the overall percentage.
     * The seeded GradingScale is the single source of truth for grading;
     * the status label is a coarse roll-up of the same scale.
     *
     * @return array{label: string, badge: string}
     */
    public function statusFor(?float $percentage): array
    {
        if ($percentage === null) {
            return ['label' => 'No Scores', 'badge' => 'secondary'];
        }

        // Reuse the seeded GradingScale for the status label (single source
        // of truth) and map it to a badge colour for the UI.
        $band = $this->gradeFor((float) $percentage);
        $label = $band['remark'] ?? 'No Band';

        return [
            'label' => $label,
            'badge' => match ($band['grade'] ?? null) {
                'A', 'B' => 'success',
                'C' => 'info',
                'D', 'E' => 'warning',
                default => 'danger',
            },
        ];
    }

    /**
     * The student's active class assignment for the given session/term.
     */
    private function currentAssignment(Student $student, int $sessionId, ?int $termId): ?StudentClassAssignment
    {
        return StudentClassAssignment::where('student_id', $student->id)
            ->where('academic_session_id', $sessionId)
            ->where('status', 'active')
            ->with(['class', 'classArm'])
            ->latest()
            ->first();
    }

    /**
     * Subjects that have at least one CA or exam score for this student/context.
     *
     * @return int[]
     */
    private function scoredSubjectIds(Student $student, int $sessionId, ?int $termId): array
    {
        $ca = AssessmentScore::where('student_id', $student->id)
            ->where('academic_session_id', $sessionId)
            ->when($termId, fn ($q) => $q->where('academic_term_id', $termId), fn ($q) => $q->whereNull('academic_term_id'))
            ->pluck('subject_id');

        $exam = ExaminationScore::where('student_id', $student->id)
            ->where('academic_session_id', $sessionId)
            ->when($termId, fn ($q) => $q->where('academic_term_id', $termId), fn ($q) => $q->whereNull('academic_term_id'))
            ->pluck('subject_id');

        return $ca->merge($exam)->unique()->values()->all();
    }

    /**
     * Build the per-subject result row.
     *
     * @return array{subject: \App\Models\Subject, ca: float|null, ca_test: float|null, exam: float|null, ca_max: float, ca_test_max: float, exam_max: float, total: float, total_max: float, percentage: float|null, grade: ?array}
     */
    private function subjectResult(Student $student, int $subjectId, int $sessionId, ?int $termId, ?ClassArm $classArm): array
    {
        // All active CA assessment types, in display order.
        $types = AssessmentType::where('is_active', true)->orderBy('display_order')->get();

        $caScores = AssessmentScore::where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->where('academic_session_id', $sessionId)
            ->when($termId, fn ($q) => $q->where('academic_term_id', $termId), fn ($q) => $q->whereNull('academic_term_id'))
            ->get()
            ->keyBy('assessment_type_id');

        $examScore = ExaminationScore::where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->where('academic_session_id', $sessionId)
            ->when($termId, fn ($q) => $q->where('academic_term_id', $termId), fn ($q) => $q->whereNull('academic_term_id'))
            ->first();

        // Classify CA types into "tests" vs "continuous". We treat the first
        // two active types as CA Test (best-fit) and the remaining as CA. This
        // keeps the report faithful to the configured assessment types without
        // hard-coding specific names.
        $testTypeIds = $types->take(2)->pluck('id')->all();

        $ca = 0.0; $caMax = 0.0;
        $caTest = 0.0; $caTestMax = 0.0;
        $hasCa = false; $hasCaTest = false;

        foreach ($types as $type) {
            $score = $caScores->get($type->id);
            if ($score === null) {
                continue;
            }
            $val = (float) $score->score;
            $max = (float) ($score->max_score ?? $type->max_score);

            if (in_array($type->id, $testTypeIds, true)) {
                $caTest += $val; $caTestMax += $max; $hasCaTest = true;
            } else {
                $ca += $val; $caMax += $max; $hasCa = true;
            }
        }

        $exam = $examScore ? (float) $examScore->score : null;
        $examMax = $examScore ? (float) ($examScore->max_score ?? 100) : 0.0;

        $total = ($hasCa ? $ca : 0.0) + ($hasCaTest ? $caTest : 0.0) + ($exam ?? 0.0);
        $totalMax = $caMax + $caTestMax + $examMax;

        $percentage = $totalMax > 0 ? ($total / $totalMax) * 100 : null;
        $grade = $percentage !== null ? $this->gradeFor((float) $percentage) : null;

        return [
            'subject' => \App\Models\Subject::find($subjectId),
            'ca' => $hasCa ? round($ca, 2) : null,
            'ca_max' => $caMax,
            'ca_test' => $hasCaTest ? round($caTest, 2) : null,
            'ca_test_max' => $caTestMax,
            'exam' => $exam !== null ? round($exam, 2) : null,
            'exam_max' => $examMax,
            'total' => round($total, 2),
            'total_max' => $totalMax,
            'percentage' => $percentage !== null ? round($percentage, 2) : null,
            'grade' => $grade,
        ];
    }

    /**
     * Attendance summary for a student within a session/term window.
     *
     * The attendance table stores per-day records. We count distinct days that
     * have any record (total school days present in the data) and the status
     * breakdown. If no records exist we return null so the UI can show
     * "No attendance data".
     *
     * @return array{days: int, present: int, absent: int, late: int, excused: int}|null
     */
    private function attendanceSummary(Student $student, int $sessionId, ?int $termId): ?array
    {
        $query = Attendance::where('student_id', $student->id);

        // Scope to the session window if a session is known.
        $session = AcademicSession::find($sessionId);
        if ($session) {
            $query->whereBetween('attendance_date', [$session->start_date, $session->end_date]);
        }

        // If a term is selected, narrow to its window.
        if ($termId) {
            $term = AcademicTerm::find($termId);
            if ($term) {
                $query->whereBetween('attendance_date', [$term->start_date, $term->end_date]);
            }
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            return null;
        }

        $byStatus = $records->groupBy('status');

        return [
            'days' => $records->count(),
            'present' => $byStatus->get('present')?->count() ?? 0,
            'absent' => $byStatus->get('absent')?->count() ?? 0,
            'late' => $byStatus->get('late')?->count() ?? 0,
            'excused' => $byStatus->get('excused')?->count() ?? 0,
        ];
    }

    /**
     * Look up the grade band for a percentage using the seeded GradingScale.
     *
     * @return array{grade: string, remark: string}|null
     */
    public function gradeFor(float $percentage): ?array
    {
        $scale = GradingScale::where('is_active', true)
            ->where('min_score', '<=', $percentage)
            ->where('max_score', '>=', $percentage)
            ->orderByDesc('min_score')
            ->first();

        if (!$scale) {
            return null;
        }

        return ['grade' => $scale->grade, 'remark' => $scale->remark];
    }

    /**
     * @return array{id: int, name: string}[]
     */
    public function sessions(): array
    {
        return AcademicSession::where('status', 'active')
            ->orderByDesc('start_date')
            ->get()
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])
            ->all();
    }

    /**
     * @return array{id: int, label: string}[]
     */
    public function termsForSession(int $sessionId): array
    {
        return AcademicTerm::where('academic_session_id', $sessionId)
            ->where('status', 'active')
            ->orderBy('start_date')
            ->get()
            ->map(fn ($t) => ['id' => $t->id, 'label' => $t->label])
            ->all();
    }
}

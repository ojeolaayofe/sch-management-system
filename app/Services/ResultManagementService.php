<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\ClassArm;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\StudentClassAssignment;

/**
 * Result Management Service
 *
 * Central Results Management for administrators. Resolves the students of a
 * selected session/term/class/class-arm and produces a per-student summary.
 * All score aggregation and grading is delegated to StudentResultService so
 * the list, the "View Result" page and the individual result PDF always use
 * the same calculation logic.
 *
 * @package SchoolHub\Services
 */
class ResultManagementService
{
    public function __construct(private StudentResultService $studentResultService)
    {
    }

    /**
     * @return array{id: int, name: string}[]
     */
    public function sessions(): array
    {
        return $this->studentResultService->sessions();
    }

    /**
     * @return array{id: int, label: string}[]
     */
    public function termsForSession(int $sessionId): array
    {
        return $this->studentResultService->termsForSession($sessionId);
    }

    /**
     * @return array<int, string> id => name
     */
    public function classes(): array
    {
        return ClassModel::orderBy('name')->pluck('name', 'id')->all();
    }

    /**
     * @return array{id: int, name: string}[]
     */
    public function armsForClass(int $classId): array
    {
        return ClassArm::where('class_id', $classId)
            ->orderBy('arm_name')
            ->get()
            ->map(fn (ClassArm $arm) => ['id' => $arm->id, 'name' => $arm->arm_name])
            ->all();
    }

    /**
     * Build the results table rows for every student assigned to the given
     * class arm in the selected session (and optionally term).
     *
     * @return array<int, array{
     *     student: Student,
     *     assignment: StudentClassAssignment,
     *     summary: array,
     *     status: array{label: string, badge: string}
     * }>
     */
    public function studentsForArm(int $sessionId, ?int $termId, int $classId, int $armId): array
    {
        // Validate the academic context up front (404 on bad ids).
        AcademicSession::findOrFail($sessionId);
        if ($termId !== null) {
            AcademicTerm::findOrFail($termId);
        }
        $arm = ClassArm::where('class_id', $classId)->findOrFail($armId);

        $assignments = StudentClassAssignment::where('academic_session_id', $sessionId)
            ->where('class_arm_id', $arm->id)
            ->where('status', 'active')
            ->orderBy('assigned_at', 'desc')
            ->get();

        $rows = [];
        foreach ($assignments as $assignment) {
            /** @var Student|null $student */
            $student = Student::find($assignment->student_id);
            if ($student === null) {
                continue;
            }

            $rows[] = [
                'student' => $student,
                'assignment' => $assignment,
                'summary' => $this->studentResultService->summaryFor($student, $sessionId, $termId),
            ];
        }

        return collect($rows)
            ->map(function (array $row) {
                $row['status'] = $this->studentResultService->statusFor($row['summary']['percentage']);
                return $row;
            })
            ->sortBy(fn ($row) => $row['student']->full_name)
            ->values()
            ->all();
    }

    /**
     * Build the full result payload for a single student in the given
     * context — the same structure used by the existing individual result
     * PDF (see StudentResultController::pdf).
     */
    public function buildResult(Student $student, int $sessionId, ?int $termId): array
    {
        return $this->studentResultService->buildResult($student, $sessionId, $termId);
    }
}

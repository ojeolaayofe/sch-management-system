<?php

namespace App\Services;

use App\Models\TeacherSubjectAssignment;
use App\Models\StudentClassAssignment;

/**
 * Teacher Dashboard Service
 *
 * Provides data for the teacher-facing dashboard and my-classes/my-subjects pages.
 *
 * @package SchoolHub\Services
 */
class TeacherDashboardService
{
    /**
     * Get dashboard stats for the authenticated teacher.
     *
     * @param int $userId The authenticated user's ID
     * @return array
     */
    public function getDashboardStats(int $userId): array
    {
        $assignments = TeacherSubjectAssignment::where('teacher_id', $userId)
            ->where('status', 'active')
            ->get();

        $classArmIds = $assignments->pluck('class_arm_id')->unique()->values();
        $subjectIds = $assignments->pluck('subject_id')->unique()->values();

        // Count students in the teacher's class arms
        $studentCount = StudentClassAssignment::where('status', 'active')
            ->whereIn('class_arm_id', $classArmIds)
            ->count();

        return [
            'assigned_classes_count' => $classArmIds->count(),
            'assigned_subjects_count' => $subjectIds->count(),
            'students_count' => $studentCount,
        ];
    }

    /**
     * Get the teacher's assigned class arms with class, subjects, and student count.
     *
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getMyClasses(int $userId)
    {
        $assignments = TeacherSubjectAssignment::where('teacher_id', $userId)
            ->where('status', 'active')
            ->with(['classArm.class', 'subject'])
            ->get();

        // Group by class_arm_id
        return $assignments
            ->groupBy('class_arm_id')
            ->map(function ($group) {
                $classArm = $group->first()->classArm;
                $subjects = $group->pluck('subject.name')->unique()->values();

                $studentCount = StudentClassAssignment::where('status', 'active')
                    ->where('class_arm_id', $group->first()->class_arm_id)
                    ->count();

                return (object) [
                    'class_arm' => $classArm,
                    'class' => $classArm->class,
                    'subjects' => $subjects,
                    'student_count' => $studentCount,
                ];
            })
            ->values();
    }

    /**
     * Get the teacher's assigned subjects with class info.
     *
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getMySubjects(int $userId)
    {
        return TeacherSubjectAssignment::where('teacher_id', $userId)
            ->where('status', 'active')
            ->with(['subject', 'classArm.class'])
            ->get()
            ->map(function ($assignment) {
                return (object) [
                    'subject' => $assignment->subject,
                    'class' => $assignment->classArm->class,
                    'class_arm' => $assignment->classArm,
                ];
            })
            ->values();
    }
}

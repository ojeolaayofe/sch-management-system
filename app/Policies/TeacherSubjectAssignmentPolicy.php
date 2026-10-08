<?php

namespace App\Policies;

use App\Models\TeacherSubjectAssignment;
use App\Models\User;

/**
 * Teacher Subject Assignment Policy
 *
 * Controls access to teacher subject assignments.
 * Teachers can only view their own assignments.
 * Admins can view all.
 *
 * @package SchoolHub\Policies
 */
class TeacherSubjectAssignmentPolicy
{
    /**
     * Determine if the user can view any assignments.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasRole('school_administrator')
            || $user->hasRole('teacher');
    }

    /**
     * Determine if the user can view the assignment.
     * Teachers can only view their own.
     */
    public function view(User $user, TeacherSubjectAssignment $assignment): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('school_administrator')) {
            return true;
        }

        // Teachers can only view their own assignments
        return $assignment->teacher_id === $user->id;
    }

    /**
     * Determine if the user can create assignments (admin only).
     */
    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    /**
     * Determine if the user can update the assignment (admin only).
     */
    public function update(User $user, TeacherSubjectAssignment $assignment): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    /**
     * Determine if the user can delete the assignment (admin only).
     */
    public function delete(User $user, TeacherSubjectAssignment $assignment): bool
    {
        return $user->hasRole('super_admin');
    }
}

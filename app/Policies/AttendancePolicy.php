<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;

/**
 * Attendance Policy
 *
 * Attendance is written by teachers via the service layer, which enforces
 * class-arm assignment server-side. This policy mirrors those rules for
 * Gate-based checks.
 *
 * @package SchoolHub\Policies
 */
class AttendancePolicy
{
    public function __construct(private AttendanceService $service)
    {
    }

    public function viewAny(User $user): bool
    {
        return $user->teacher !== null;
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Teachers may update attendance only for class arms they are assigned to.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->teacher === null) {
            return false;
        }

        return $this->service->isAssignedToClassArm($user->id, (int) $attendance->class_arm_id);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->hasRole('super_admin');
    }
}

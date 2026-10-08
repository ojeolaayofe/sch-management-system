<?php

namespace App\Policies;

/**
 * Class Arm Policy
 *
 * @package SchoolHub\Policies
 */
class ClassArmPolicy
{
    public function viewAny(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function view(\App\Models\User $user, \App\Models\ClassArm $arm): bool
    {
        return $this->viewAny($user);
    }

    public function create(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function update(\App\Models\User $user, \App\Models\ClassArm $arm): bool
    {
        return $this->create($user);
    }

    public function delete(\App\Models\User $user, \App\Models\ClassArm $arm): bool
    {
        return $user->hasRole('super_admin');
    }
}

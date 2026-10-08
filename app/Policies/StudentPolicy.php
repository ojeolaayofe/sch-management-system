<?php

namespace App\Policies;

/**
 * Student Policy
 *
 * @package SchoolHub\Policies
 */
class StudentPolicy
{
    public function viewAny(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function view(\App\Models\User $user, \App\Models\Student $student): bool
    {
        return $this->viewAny($user);
    }

    public function create(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function update(\App\Models\User $user, \App\Models\Student $student): bool
    {
        return $this->create($user);
    }

    public function delete(\App\Models\User $user, \App\Models\Student $student): bool
    {
        return $user->hasRole('super_admin');
    }
}

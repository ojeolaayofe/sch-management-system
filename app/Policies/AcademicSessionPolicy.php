<?php

namespace App\Policies;

/**
 * Academic Session Policy
 *
 * @package SchoolHub\Policies
 */
class AcademicSessionPolicy
{
    public function viewAny(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function view(\App\Models\User $user, \App\Models\AcademicSession $session): bool
    {
        return $this->viewAny($user);
    }

    public function create(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function update(\App\Models\User $user, \App\Models\AcademicSession $session): bool
    {
        return $this->create($user);
    }

    public function delete(\App\Models\User $user, \App\Models\AcademicSession $session): bool
    {
        return $user->hasRole('super_admin');
    }
}

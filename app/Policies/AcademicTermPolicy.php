<?php

namespace App\Policies;

/**
 * Academic Term Policy
 *
 * @package SchoolHub\Policies
 */
class AcademicTermPolicy
{
    public function viewAny(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function view(\App\Models\User $user, \App\Models\AcademicTerm $term): bool
    {
        return $this->viewAny($user);
    }

    public function create(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function update(\App\Models\User $user, \App\Models\AcademicTerm $term): bool
    {
        return $this->create($user);
    }

    public function delete(\App\Models\User $user, \App\Models\AcademicTerm $term): bool
    {
        return $user->hasRole('super_admin');
    }
}

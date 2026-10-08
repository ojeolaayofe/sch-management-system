<?php

namespace App\Policies;

/**
 * Class Policy
 *
 * @package SchoolHub\Policies
 */
class ClassPolicy
{
    public function viewAny(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function view(\App\Models\User $user, \App\Models\ClassModel $class): bool
    {
        return $this->viewAny($user);
    }

    public function create(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('school_administrator');
    }

    public function update(\App\Models\User $user, \App\Models\ClassModel $class): bool
    {
        return $this->create($user);
    }

    public function delete(\App\Models\User $user, \App\Models\ClassModel $class): bool
    {
        return $user->hasRole('super_admin');
    }
}

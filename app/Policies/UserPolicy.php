<?php

namespace App\Policies;

/**
 * User Policy
 * 
 * Defines authorization logic for User model.
 * 
 * @package SchoolHub\Policies
 */
class UserPolicy
{
    /**
     * Determine if the user can view any models.
     */
    public function viewAny(\App\Models\User $user): bool
    {
        return $user->hasRoleToSpatie('super_admin') || $user->hasRoleToSpatie('school_administrator');
    }
    
    /**
     * Determine if the user can view the model.
     */
    public function view(\App\Models\User $user, \App\Models\User $model): bool
    {
        return $user->id === $model->id || 
               $user->hasRoleToSpatie('super_admin') || 
               $user->hasRoleToSpatie('school_administrator');
    }
    
    /**
     * Determine if the user can create models.
     */
    public function create(\App\Models\User $user): bool
    {
        return $user->hasRoleToSpatie('super_admin') || $user->hasRoleToSpatie('school_administrator');
    }
    
    /**
     * Determine if the user can update the model.
     */
    public function update(\App\Models\User $user, \App\Models\User $model): bool
    {
        return $user->id === $model->id || 
               $user->hasRoleToSpatie('super_admin') || 
               $user->hasRoleToSpatie('school_administrator');
    }
    
    /**
     * Determine if the user can delete the model.
     */
    public function delete(\App\Models\User $user, \App\Models\User $model): bool
    {
        return $user->hasRoleToSpatie('super_admin') && $user->id !== $model->id;
    }
    
    /**
     * Determine if the user can restore the model.
     */
    public function restore(\App\Models\User $user, \App\Models\User $model): bool
    {
        return $user->hasRoleToSpatie('super_admin');
    }
    
    /**
     * Determine if the user can permanently delete the model.
     */
    public function forceDelete(\App\Models\User $user, \App\Models\User $model): bool
    {
        return $user->hasRoleToSpatie('super_admin');
    }
}

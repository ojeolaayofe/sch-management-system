<?php

namespace App\Services;

/**
 * User Service
 * 
 * Handles all user-related business logic.
 * 
 * @package SchoolHub\Services
 */
class UserService
{
    /**
     * Create a new user
     * 
     * @param array<string, mixed> $data
     * @return \App\Models\User
     */
    public function create(array $data): \App\Models\User
    {
        return \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'status' => 'active',
        ]);
    }
    
    /**
     * Update user profile
     * 
     * @param \App\Models\User $user
     * @param array<string, mixed> $data
     * @return \App\Models\User
     */
    public function updateProfile(\App\Models\User $user, array $data): \App\Models\User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);
        
        return $user->fresh();
    }
    
    /**
     * Change user password
     * 
     * @param \App\Models\User $user
     * @param string $oldPassword
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(\App\Models\User $user, string $oldPassword, string $newPassword): bool
    {
        if (!\Hash::check($oldPassword, $user->password)) {
            return false;
        }
        
        $user->password = \Hash::make($newPassword);
        return $user->save();
    }
    
    /**
     * Assign role to user
     * 
     * @param \App\Models\User $user
     * @param string $role
     * @return void
     */
    public function assignRole(\App\Models\User $user, string $role): void
    {
        $user->syncRoles([]);
        $user->assignRole($role);
    }
}

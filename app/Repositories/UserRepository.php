<?php

namespace App\Repositories;

/**
 * User Repository Implementation
 * 
 * @package SchoolHub\Repositories
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * Find user by ID
     * 
     * @param int $id
     * @return \App\Models\User|null
     */
    public function find(int $id): ?\App\Models\User
    {
        return \App\Models\User::with('roles')->find($id);
    }
    
    /**
     * Find user by email
     * 
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email): ?\App\Models\User
    {
        return \App\Models\User::where('email', $email)->first();
    }
    
    /**
     * Get all users
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $perPage = 15)
    {
        return \App\Models\User::with('roles')->latest()->paginate($perPage);
    }
    
    /**
     * Create user
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
            'status' => $data['status'] ?? 'active',
        ]);
    }
    
    /**
     * Update user
     * 
     * @param \App\Models\User $user
     * @param array<string, mixed> $data
     * @return \App\Models\User
     */
    public function update(\App\Models\User $user, array $data): \App\Models\User
    {
        $user->update($data);
        return $user->fresh();
    }
    
    /**
     * Delete user
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    public function delete(\App\Models\User $user): bool
    {
        return $user->delete();
    }
}

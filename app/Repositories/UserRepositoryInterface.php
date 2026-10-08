<?php

namespace App\Repositories;

/**
 * User Repository Interface
 * 
 * @package SchoolHub\Repositories
 */
interface UserRepositoryInterface
{
    /**
     * Find user by ID
     * 
     * @param int $id
     * @return \App\Models\User|null
     */
    public function find(int $id): ?\App\Models\User;
    
    /**
     * Find user by email
     * 
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email): ?\App\Models\User;
    
    /**
     * Get all users
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $perPage = 15); 
    
    /**
     * Create user
     * 
     * @param array<string, mixed> $data
     * @return \App\Models\User
     */
    public function create(array $data): \App\Models\User;
    
    /**
     * Update user
     * 
     * @param \App\Models\User $user
     * @param array<string, mixed> $data
     * @return \App\Models\User
     */
    public function update(\App\Models\User $user, array $data): \App\Models\User;
    
    /**
     * Delete user
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    public function delete(\App\Models\User $user): bool;
}

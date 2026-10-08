<?php

namespace App\Repositories;

/**
 * Base Repository
 * 
 * Provides common repository methods for all repositories.
 * 
 * @package SchoolHub\Repositories
 * @template T of \Illuminate\Database\Eloquent\Model
 */
abstract class BaseRepository
{
    /**
     * The model instance.
     * 
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected \Illuminate\Database\Eloquent\Model $model;
    
    /**
     * Items per page for pagination.
     * 
     * @var int
     */
    protected int $perPage = 15;
    
    /**
     * Constructor
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(\Illuminate\Database\Eloquent\Model $model)
    {
        $this->model = $model;
    }
    
    /**
     * Find by ID
     * 
     * @param int $id
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find(int $id, array $with = []): ?\Illuminate\Database\Eloquent\Model
    {
        return empty($with)
            ? $this->model->find($id)
            : $this->model->with($with)->find($id);
    }
    
    /**
     * Find by ID or fail
     * 
     * @param int $id
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(int $id, array $with = []): \Illuminate\Database\Eloquent\Model
    {
        return empty($with)
            ? $this->model->findOrFail($id)
            : $this->model->with($with)->findOrFail($id);
    }
    
    /**
     * Get all records with pagination
     * 
     * @param int $perPage
     * @param array $with
     * @param string $orderBy
     * @param string $orderDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function all(
        int $perPage = 0,
        array $with = [],
        string $orderBy = 'id',
        string $orderDirection = 'desc'
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $perPage = $perPage ?: $this->perPage;
        $query = empty($with) ? $this->model::query() : $this->model->with($with);
        
        return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
    }
    
    /**
     * Create a new record
     * 
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        return $this->model->create($data);
    }
    
    /**
     * Update a record
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(\Illuminate\Database\Eloquent\Model $model, array $data): bool
    {
        return $model->fill($data)->save();
    }
    
    /**
     * Delete a record
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function delete(\Illuminate\Database\Eloquent\Model $model): bool
    {
        return $model->delete();
    }
    
    /**
     * Get a new query builder instance
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model::query();
    }
    
    /**
     * Count records
     * 
     * @return int
     */
    public function count(): int
    {
        return $this->model::count();
    }
}

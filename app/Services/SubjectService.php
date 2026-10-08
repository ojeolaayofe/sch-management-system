<?php

namespace App\Services;

use App\Repositories\SubjectRepository;

/**
 * Subject Service
 *
 * @package SchoolHub\Services
 */
class SubjectService
{
    private SubjectRepository $repository;

    public function __construct(SubjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): \App\Models\Subject
    {
        return $this->repository->create($data);
    }

    public function update(\App\Models\Subject $subject, array $data): bool
    {
        return $this->repository->update($subject, $data);
    }

    public function delete(\App\Models\Subject $subject): bool
    {
        return $this->repository->delete($subject);
    }

    public function getAll(int $perPage = 15, string $search = '', string $status = 'all', string $category = 'all')
    {
        $query = $this->repository->query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        return $query->latest()->paginate($perPage);
    }
}

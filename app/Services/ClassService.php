<?php

namespace App\Services;

use App\Repositories\ClassRepository;

/**
 * Class Service
 *
 * @package SchoolHub\Services
 */
class ClassService
{
    private ClassRepository $repository;

    public function __construct(ClassRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): \App\Models\ClassModel
    {
        return $this->repository->create($data);
    }

    public function update(\App\Models\ClassModel $class, array $data): bool
    {
        return $this->repository->update($class, $data);
    }

    public function delete(\App\Models\ClassModel $class): bool
    {
        return $this->repository->delete($class);
    }

    public function getAll(int $perPage = 15, string $search = '', string $status = 'all', string $section = 'all')
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

        if ($section !== 'all') {
            $query->where('section', $section);
        }

        return $query->orderBy('display_order')->paginate($perPage);
    }
}

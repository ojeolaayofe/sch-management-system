<?php

namespace App\Services;

use App\Repositories\ClassArmRepository;

/**
 * Class Arm Service
 *
 * @package SchoolHub\Services
 */
class ClassArmService
{
    private ClassArmRepository $repository;

    public function __construct(ClassArmRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): \App\Models\ClassArm
    {
        return $this->repository->create($data);
    }

    public function update(\App\Models\ClassArm $arm, array $data): bool
    {
        return $this->repository->update($arm, $data);
    }

    public function delete(\App\Models\ClassArm $arm): bool
    {
        return $this->repository->delete($arm);
    }

    public function getAll(int $perPage = 15, string $search = '', string $status = 'all', int $classId = 0)
    {
        $query = $this->repository->query()->with(['class', 'classTeacher']);

        if ($search) {
            $query->where('arm_name', 'like', "%{$search}%");
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($classId > 0) {
            $query->where('class_id', $classId);
        }

        return $query->latest()->paginate($perPage);
    }
}

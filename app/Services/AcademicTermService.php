<?php

namespace App\Services;

use App\Repositories\AcademicTermRepository;

/**
 * Academic Term Service
 *
 * @package SchoolHub\Services
 */
class AcademicTermService
{
    private AcademicTermRepository $repository;

    public function __construct(AcademicTermRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): \App\Models\AcademicTerm
    {
        return $this->repository->create($data);
    }

    public function update(\App\Models\AcademicTerm $term, array $data): bool
    {
        return $this->repository->update($term, $data);
    }

    public function delete(\App\Models\AcademicTerm $term): bool
    {
        return $this->repository->delete($term);
    }

    public function setCurrent(int $termId): void
    {
        $this->repository->setCurrent($termId);
    }

    public function getAll(int $perPage = 15, string $search = '', string $status = 'all', int $sessionId = 0)
    {
        $query = $this->repository->query()->with('academicSession');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($sessionId > 0) {
            $query->where('academic_session_id', $sessionId);
        }

        return $query->latest()->paginate($perPage);
    }
}

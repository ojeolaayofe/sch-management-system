<?php

namespace App\Services;

use App\Repositories\AcademicSessionRepository;

/**
 * Academic Session Service
 *
 * @package SchoolHub\Services
 */
class AcademicSessionService
{
    /**
     * Repository instance
     */
    private AcademicSessionRepository $repository;

    /**
     * Constructor
     *
     * @param AcademicSessionRepository $repository
     */
    public function __construct(AcademicSessionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new academic session
     *
     * @param array<string, mixed> $data
     * @return \App\Models\AcademicSession
     */
    public function create(array $data): \App\Models\AcademicSession
    {
        return $this->repository->create($data);
    }

    /**
     * Update an academic session
     *
     * @param \App\Models\AcademicSession $session
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(\App\Models\AcademicSession $session, array $data): bool
    {
        return $this->repository->update($session, $data);
    }

    /**
     * Delete an academic session
     *
     * @param \App\Models\AcademicSession $session
     * @return bool
     */
    public function delete(\App\Models\AcademicSession $session): bool
    {
        return $this->repository->delete($session);
    }

    /**
     * Set current session
     *
     * @param int $sessionId
     * @return void
     */
    public function setCurrent(int $sessionId): void
    {
        $this->repository->setCurrent($sessionId);
    }

    /**
     * Get all sessions with pagination
     *
     * @param int $perPage
     * @param string $search
     * @param string $status
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(int $perPage = 15, string $search = '', string $status = 'all')
    {
        $query = $this->repository->query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return $query->latest()->paginate($perPage);
    }
}

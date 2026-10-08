<?php

namespace App\Services;

use App\Repositories\StudentRepository;
use App\Models\Student;

/**
 * Student Service
 *
 * @package SchoolHub\Services
 */
class StudentService
{
    private StudentRepository $repository;

    public function __construct(StudentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new student
     */
    public function create(array $data): Student
    {
        if (empty($data['student_id'])) {
            $data['student_id'] = Student::generateStudentId();
        }
        return $this->repository->create($data);
    }

    /**
     * Update a student
     */
    public function update(Student $student, array $data): bool
    {
        return $this->repository->update($student, $data);
    }

    /**
     * Delete a student
     */
    public function delete(Student $student): bool
    {
        return $this->repository->delete($student);
    }

    /**
     * Get all students with pagination and filters
     */
    public function getAll(
        int $perPage = 15,
        string $search = '',
        string $status = 'all',
        int $classId = 0,
        int $sessionId = 0
    ) {
        $query = $this->repository->query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return $query->orderBy('last_name')->paginate($perPage);
    }

    /**
     * Find student by ID with relationships
     */
    public function findWithRelations(int $id)
    {
        return $this->repository->findOrFail($id, ['classAssignments', 'classAssignments.class', 'classAssignments.classArm']);
    }
}

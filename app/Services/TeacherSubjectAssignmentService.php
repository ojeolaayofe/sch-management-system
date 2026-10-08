<?php

namespace App\Services;

use App\Repositories\TeacherSubjectAssignmentRepository;

/**
 * Teacher Subject Assignment Service
 *
 * @package SchoolHub\Services
 */
class TeacherSubjectAssignmentService
{
    private TeacherSubjectAssignmentRepository $repository;

    public function __construct(TeacherSubjectAssignmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): \App\Models\TeacherSubjectAssignment
    {
        return $this->repository->create($data);
    }

    public function update(\App\Models\TeacherSubjectAssignment $assignment, array $data): bool
    {
        return $this->repository->update($assignment, $data);
    }

    public function delete(\App\Models\TeacherSubjectAssignment $assignment): bool
    {
        return $this->repository->delete($assignment);
    }

    public function getAll(int $perPage = 15, string $search = '', string $status = 'all', int $teacherId = 0, int $subjectId = 0)
    {
        $query = $this->repository->query()->with(['teacher', 'subject', 'classArm.class', 'academicSession']);

        if ($search) {
            $query->whereHas('teacher', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($teacherId > 0) {
            $query->where('teacher_id', $teacherId);
        }

        if ($subjectId > 0) {
            $query->where('subject_id', $subjectId);
        }

        return $query->latest()->paginate($perPage);
    }
}

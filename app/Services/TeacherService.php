<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\DB;

/**
 * Teacher Service
 *
 * Handles teacher business logic:
 * - Creates User + Teacher in a single transaction
 * - Assigns the Spatie 'teacher' role
 * - Uses the existing 'web' auth guard
 *
 * @package SchoolHub\Services
 */
class TeacherService
{
    private TeacherRepository $repository;

    public function __construct(TeacherRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new teacher with an associated user account.
     *
     * If email and password are provided, a User record is created,
     * the 'teacher' Spatie role is assigned, and the user_id is linked.
     *
     * Everything runs inside a database transaction.
     */
    public function create(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            // 1. Generate teacher ID if not provided
            if (empty($data['teacher_id'])) {
                $data['teacher_id'] = Teacher::generateTeacherId();
            }

            // 2. Create user account if email and password are provided
            if (!empty($data['email']) && !empty($data['password'])) {
                $fullName = trim(
                    ($data['first_name'] ?? '') . ' ' .
                    ($data['middle_name'] ?? '') . ' ' .
                    ($data['last_name'] ?? '')
                );

                $user = User::create([
                    'name'     => $fullName,
                    'email'    => $data['email'],
                    'password' => bcrypt($data['password']),
                ]);

                // Assign the 'teacher' Spatie role
                $user->assignRole('teacher');

                $data['user_id'] = $user->id;
            }

            // 3. Remove password-related fields before creating Teacher
            unset($data['password'], $data['password_confirmation']);

            // 4. Create the Teacher profile
            return $this->repository->create($data);
        });
    }

    /**
     * Update a teacher profile.
     */
    public function update(Teacher $teacher, array $data): bool
    {
        return $this->repository->update($teacher, $data);
    }

    /**
     * Update the teacher's associated user password.
     */
    public function updatePassword(Teacher $teacher, string $password): bool
    {
        if (! $teacher->user) {
            return false;
        }

        return $teacher->user->update(['password' => bcrypt($password)]) !== false;
    }

    /**
     * Soft-delete a teacher.
     * Also removes the 'teacher' role from the associated user.
     */
    public function delete(Teacher $teacher): bool
    {
        DB::transaction(function () use ($teacher) {
            // Remove teacher role from user
            if ($teacher->user) {
                $teacher->user->removeRole('teacher');
            }

            $this->repository->delete($teacher);
        });

        return true;
    }

    /**
     * Get all teachers with pagination and filters.
     */
    public function getAll(
        int $perPage = 15,
        string $search = '',
        string $status = 'all'
    ) {
        $query = $this->repository->query()->with(['user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('teacher_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return $query->orderBy('last_name')->paginate($perPage);
    }

    /**
     * Find a teacher by ID with user relationship eager-loaded.
     */
    public function findWithUser(int $id): Teacher
    {
        return $this->repository->findOrFail($id, ['user']);
    }

    /**
     * Get the total teacher count.
     */
    public function count(): int
    {
        return $this->repository->count();
    }
}

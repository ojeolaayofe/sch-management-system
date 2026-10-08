<?php

namespace App\Repositories;

use App\Models\Student;

/**
 * Student Repository
 *
 * @package SchoolHub\Repositories
 */
class StudentRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param Student $model
     */
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }
}

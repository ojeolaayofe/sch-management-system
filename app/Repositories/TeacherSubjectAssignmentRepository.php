<?php

namespace App\Repositories;

use App\Models\TeacherSubjectAssignment;

/**
 * Teacher Subject Assignment Repository
 *
 * @package SchoolHub\Repositories
 */
class TeacherSubjectAssignmentRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param TeacherSubjectAssignment $model
     */
    public function __construct(TeacherSubjectAssignment $model)
    {
        parent::__construct($model);
    }
}

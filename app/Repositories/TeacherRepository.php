<?php

namespace App\Repositories;

use App\Models\Teacher;

/**
 * Teacher Repository
 *
 * @package SchoolHub\Repositories
 */
class TeacherRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param Teacher $model
     */
    public function __construct(Teacher $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace App\Repositories;

use App\Models\Subject;

/**
 * Subject Repository
 *
 * @package SchoolHub\Repositories
 */
class SubjectRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param Subject $model
     */
    public function __construct(Subject $model)
    {
        parent::__construct($model);
    }
}

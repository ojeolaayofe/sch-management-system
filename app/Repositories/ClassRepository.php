<?php

namespace App\Repositories;

use App\Models\ClassModel;

/**
 * Class Repository
 *
 * @package SchoolHub\Repositories
 */
class ClassRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param ClassModel $model
     */
    public function __construct(ClassModel $model)
    {
        parent::__construct($model);
    }
}

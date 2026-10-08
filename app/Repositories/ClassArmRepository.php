<?php

namespace App\Repositories;

use App\Models\ClassArm;

/**
 * Class Arm Repository
 *
 * @package SchoolHub\Repositories
 */
class ClassArmRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param ClassArm $model
     */
    public function __construct(ClassArm $model)
    {
        parent::__construct($model);
    }
}

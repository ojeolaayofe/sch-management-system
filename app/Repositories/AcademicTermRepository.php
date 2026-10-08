<?php

namespace App\Repositories;

use App\Models\AcademicTerm;

/**
 * Academic Term Repository
 *
 * @package SchoolHub\Repositories
 */
class AcademicTermRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param AcademicTerm $model
     */
    public function __construct(AcademicTerm $model)
    {
        parent::__construct($model);
    }

    /**
     * Set current term
     *
     * @param int $termId
     * @return void
     */
    public function setCurrent(int $termId): void
    {
        $this->model->where('is_current', true)->update(['is_current' => false]);
        $this->model->find($termId)->update(['is_current' => true]);
    }
}

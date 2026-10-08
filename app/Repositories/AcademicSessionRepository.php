<?php

namespace App\Repositories;

use App\Models\AcademicSession;

/**
 * Academic Session Repository
 *
 * @package SchoolHub\Repositories
 */
class AcademicSessionRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param AcademicSession $model
     */
    public function __construct(AcademicSession $model)
    {
        parent::__construct($model);
    }

    /**
     * Set current session
     *
     * @param int $sessionId
     * @return void
     */
    public function setCurrent(int $sessionId): void
    {
        $this->model->where('is_current', true)->update(['is_current' => false]);
        $this->model->find($sessionId)->update(['is_current' => true]);
    }
}

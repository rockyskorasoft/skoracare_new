<?php

namespace App\Repositories;

use App\Models\Activity;
use App\Repositories\BaseRepository;

class ActivityLogRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(Activity $model)
    {
        parent::__construct($model);
    }

}

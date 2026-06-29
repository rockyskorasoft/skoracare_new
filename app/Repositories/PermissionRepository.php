<?php

namespace App\Repositories;

use App\Models\Permission;

class PermissionRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }
}

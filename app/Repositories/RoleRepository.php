<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    /**
     * Extract role fields from the request.
     */
    public function getDataFromRequest($request): array
    {
        return array_merge(
            [
                'parents' => [],
                'children' => [],
            ],
            $request->only([
                'name',
                'parents',
                'children',
            ])
        );
    }
}

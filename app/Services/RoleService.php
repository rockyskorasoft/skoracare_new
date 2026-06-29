<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\RoleRepository;

class RoleService
{
    /**
     * Create a new class instance.
     */
    public function __construct(public RoleRepository $roleRepository){}

    /**
     * Delegate get all data request to the repository.
     *
     * @param  array  $data
     * @return mixed
     */
    public function getData()
    {
        return Role::orderBy('name')->get();
    }
}

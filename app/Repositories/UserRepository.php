<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     *
     * Inject the User model so BaseRepository can perform CRUD operations on it.
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getDataById(string $id)
    {
        $data = $this->model::find($id);
        if (! $data) {
            throw new \Exception(trans('app.data_not_found'));
        }

        return $data;
    }

    public function updateData(string $id, array $updatedData)
    {
        $data = $this->getDataById($id);
        $data->update($updatedData);

        return $data;
    }

    public function deleteDataById(string $id)
    {
        $data = $this->getDataById($id);
        $data->delete();

        return $data;
    }
}

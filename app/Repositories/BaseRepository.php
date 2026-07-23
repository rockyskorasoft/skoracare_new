<?php

namespace App\Repositories;

use App\Support\SecureRouteParameter;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    protected $model;

    /**
     * Create a new class instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * This function retrieves All data.
     */
    public function getAllData()
    {
        return $this->model->get();
    }

    /**
     * function to get the data by id (handles encrypted or plain integer IDs safely).
     *
     * @param string $id
     * @return object
     */
    public function getDataById(string $id)
    {
        $decodedId = SecureRouteParameter::decode($id) ?? $id;
        $data = $this->model::find($decodedId);
        if (!$data) {
            abort(404, trans('app.data_not_found') ?: 'Requested data not found');
        }
        return $data;
    }

    /**
     * Create a new record for the current model.
     *
     * @param array $data The validated model attributes.
     * @return object The created model instance.
     */
    public function createData(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update record data by ID.
     */
    public function updateData(string $id, array $updatedData)
    {
        $decodedId = SecureRouteParameter::decode($id) ?? $id;
        $data = $this->model::find($decodedId);
        if (!$data) {
            abort(404, trans('app.data_not_found') ?: 'Requested data not found');
        }
        $data->update($updatedData);
        return $data;
    }

    /**
     * Delete record by ID.
     */
    public function deleteDataById(string $id)
    {
        $decodedId = SecureRouteParameter::decode($id) ?? $id;
        $data = $this->model::find($decodedId);
        if (!$data) {
            abort(404, trans('app.data_not_found') ?: 'Requested data not found');
        }
        $data->delete();
        return $data;
    }

    /**
     * Extract request attributes.
     */
    public function getAllDataFromRequest($request)
    {
        return $request->only([]);
    }

    /**
     * Get data based on filter array.
     */
    public function getDataOnBasisOfFilter(array $filters): Collection
    {
        $query = $this->model::query();
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }
}

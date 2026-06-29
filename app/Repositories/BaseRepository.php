<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

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
     * This function retrieves an All data.
     *
     */
    public function getAllData()
    {
        return $this->model->get();
    }

    /**
     * function to get the user data by id
     *
     * @param integer $dataId
     * @return object
     */
    public function getDataById(string $id)
    {
        $data =  $this->model::find($id);
        if (!$data) {
            throw new Exception(trans('app.data_not_found'));
        }
        return $data;
    }

    /*
    * Create a new record for the current model.
    * This is the repository-level create operation used by service classes.
    *
    * @param array $data The validated model attributes.
    * @return object The created model instance.
    */
    public function createData(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * This function get data value from dataId  and update data
     */
    public function updateData(string $id, array $updatedData)
    {
        $data = $this->model::findOrFail($id);
        if (!$data) {
            throw new Exception(trans('app.data_not_found'));
        }
        $data->update($updatedData);
        return $data;
    }

    /**
     * This function get Company value from companyId  and delete Company
     */

    public function deleteDataById(string $id)
    {
        $data = $this->model::find($id);
        if (!$data) {
            throw new Exception(trans('app.data_not_found'));
        }
        $data->delete();
        return $data;
    }

    /*
    * function to validate the data which is coming from the request
    *
    * @param object $requestData
    * @return array
    */
    public function getAllDataFromRequest($request)
    {
        return $request->only([]);
    }

    /**
     * function to get the data of the basis of filter passed
     *
     * @param array $filters
     * @return mixed
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

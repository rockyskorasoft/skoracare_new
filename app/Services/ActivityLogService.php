<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;

class ActivityLogService
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ActivityLogRepository $logRepository){}

    /**
     * Delegate get all data request to the repository.
     *
     * @param array $data
     * @return mixed
     */
    public function getData()
    {
        return $this->logRepository->getAllData();
    }

    /**
     * Delegate get data by id request to the repository.
     *
     * @param string $id
     * @return mixed
     */
    public function getDataById(string $id)
    {
        return $this->logRepository->getDataById($id);
    }
}
    
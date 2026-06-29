<?php

namespace App\Services;

use App\Repositories\ClinicRepository;

class ClinicService
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ClinicRepository $clinicRepository) {}

    /**
     * Extract only the relevant clinic fields from the incoming request.
     */
    public function getDataFromRequest($request)
    {
        return $this->clinicRepository->getDataFromRequest($request);
    }

    /**
     * Get all clinics from the repository.
     */
    public function getData()
    {
        return $this->clinicRepository->getAllData();
    }

    /**
     * Delegate create data request to the repository.
     */
    public function createData(array $data)
    {
        return $this->clinicRepository->createData($data);
    }

    /**
     * Delegate get data by id request to the repository.
     */
    public function getDataById(string $id)
    {
        return $this->clinicRepository->getDataById($id);
    }

    /**
     * Delegate update data request to the repository.
     */
    public function updateData(string $id, array $data)
    {
        return $this->clinicRepository->updateData($id, $data);
    }

    /**
     * Delegate delete data request to the repository.
     */
    public function deleteDataById(string $id)
    {
        return $this->clinicRepository->deleteDataById($id);
    }
}

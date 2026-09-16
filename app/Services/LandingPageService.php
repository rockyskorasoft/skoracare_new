<?php

namespace App\Services;

use App\Repositories\LandingPageRepository;

class LandingPageService
{
    public function __construct(public LandingPageRepository $landingPageRepository) {}

    public function getDataFromRequest($request): array
    {
        return $this->landingPageRepository->getDataFromRequest($request);
    }

    public function getData()
    {
        return $this->landingPageRepository->getAllData();
    }

    public function createData(array $data)
    {
        return $this->landingPageRepository->createData($data);
    }

    public function getDataById(string $id)
    {
        return $this->landingPageRepository->getDataById($id);
    }

    public function updateData(string $id, array $data)
    {
        return $this->landingPageRepository->updateData($id, $data);
    }

    public function deleteDataById(string $id)
    {
        return $this->landingPageRepository->deleteDataById($id);
    }
}

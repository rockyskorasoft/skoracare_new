<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(public UserRepository $userRepository) {}

    /**
     * Extract only the relevant user fields from the incoming request.
     * This keeps the controller clean and ensures only allowed data is passed to the repository.
     *
     * @param  mixed  $request
     * @return mixed
     */
    public function getDataFromRequest($request)
    {
        return $request->only(
            [
                'first_name',
                'last_name',
                'email',
                'phone_no',
                'password',
                'role',
                'status',
                'address',
                'qualification',
                'registration_number',
                'package_id',
            ]
        );
    }

    /**
     * Get all users from the repository.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->userRepository->getAllData();
    }

    /**
     * Delegate create data request to the repository.
     * The service layer acts as a thin boundary between controller and repository.
     *
     * @return mixed
     */
    public function createData(array $data)
    {
        return $this->userRepository->createData($data);
    }

    /**
     * Delegate get data by id request to the repository.
     *
     * @return mixed
     */
    public function getDataById(string $id)
    {
        return $this->userRepository->getDataById($id);
    }

    /**
     * Delegate update data request to the repository.
     *
     * @return mixed
     */
    public function updateData(string $id, array $data)
    {
        return $this->userRepository->updateData($id, $data);
    }

    /**
     * Delegate delete data request to the repository.
     *
     * @return mixed
     */
    public function deleteDataById(string $id)
    {
        return $this->userRepository->deleteDataById($id);
    }
}

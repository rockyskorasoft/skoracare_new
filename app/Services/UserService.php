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
                'max_clinics',
                'max_users',
                'package_expires_at',
            ]
        );
    }

    /**
     * Check if a doctor or staff user's associated doctor subscription has expired.
     *
     * @param User|null $user
     * @return array
     */
    public function checkPackageExpiry(?User $user = null): array
    {
        /** @var User|null $currentUser */
        $currentUser = $user ?? auth()->user();

        $supportPhone = config('constants.support_phone', '+91 921 7375 831 / 835');
        $supportEmail = config('constants.support_email', 'info@skoracares.com');

        if (! $currentUser) {
            return [
                'is_expired' => false,
                'is_doctor' => false,
                'doctor_name' => '',
                'package_name' => '',
                'expires_at' => null,
                'support_phone' => $supportPhone,
                'support_email' => $supportEmail,
            ];
        }

        // Super Admin & Admin never expire
        $isSuperAdminOrAdmin = $currentUser->hasRole([
            config('constants.super_admin_role_name'),
            config('constants.admin_role_name'),
        ]);

        if ($isSuperAdminOrAdmin) {
            return [
                'is_expired' => false,
                'is_doctor' => false,
                'doctor_name' => $currentUser->name,
                'package_name' => 'Unlimited',
                'expires_at' => null,
                'support_phone' => $supportPhone,
                'support_email' => $supportEmail,
            ];
        }

        $isDoctor = $currentUser->hasRole(config('constants.doctor_role_name'));
        $doctor = null;

        if ($isDoctor) {
            $doctor = $currentUser;
        } else {
            // For staff/other clinic users: identify parent creator doctor or clinic owner doctor
            if ($currentUser->creator && $currentUser->creator->hasRole(config('constants.doctor_role_name'))) {
                $doctor = $currentUser->creator;
            } else {
                $assignedClinic = $currentUser->assignedClinics()->first();
                if ($assignedClinic && $assignedClinic->doctor) {
                    $doctor = $assignedClinic->doctor;
                }
            }
        }

        if (! $doctor) {
            return [
                'is_expired' => false,
                'is_doctor' => $isDoctor,
                'doctor_name' => $currentUser->name,
                'package_name' => '',
                'expires_at' => null,
                'support_phone' => $supportPhone,
                'support_email' => $supportEmail,
            ];
        }

        $expiresAt = $doctor->package_expires_at;
        $isExpired = $expiresAt ? $expiresAt->isPast() : false;

        return [
            'is_expired' => $isExpired,
            'is_doctor' => $isDoctor,
            'doctor_name' => $doctor->name,
            'package_name' => $doctor->package ? $doctor->package->name : 'Trial Package',
            'expires_at' => $expiresAt ? $expiresAt->format('d M, Y') : null,
            'support_phone' => $supportPhone,
            'support_email' => $supportEmail,
        ];
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

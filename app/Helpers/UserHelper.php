<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserHelper
{
    private static $authClass = Auth::class;

    /**
     * function to get the logged-in user data
     *
     * @return object
     */
    public static function getLoggedInUser()
    {
        $userData = null;
        if (self::$authClass::check()) {
            $userData = self::$authClass::user();
        }
        return $userData;
    }

    /**
     * function to upload the profile photo
     *
     * @param mixed $fileData
     * @return string
     */
    public static function uploadImage($fileData, $destinationPath)
    {
        if (!Storage::disk('public')->exists($destinationPath)) {
            Storage::disk('public')->makeDirectory($destinationPath);
        }
        return $fileData->store($destinationPath, 'public');
    }

    /**
     * function to delete the uploaded file
     *
     * @param object $user
     * @return void
     */
    public static function deleteImage($directory, $filename)
    {
        $filePath = $directory . '/' . $filename;
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }
        return false;
    }

    /**
     * Get the active/selected clinic ID from session or user context.
     *
     * @return int|null
     */
    public static function getSelectedClinicId()
    {
        if (!Auth::check()) {
            return null;
        }

        $activeClinicId = session('active_clinic_id');

        if ($activeClinicId === 'all') {
            return null;
        }

        if (!empty($activeClinicId) && is_numeric($activeClinicId)) {
            return (int) $activeClinicId;
        }

        $user = Auth::user();
        if ($user) {
            $isSuperAdminOrAdmin = $user->hasRole([
                config('constants.super_admin_role_name'),
                config('constants.admin_role_name'),
            ]);

            if ($isSuperAdminOrAdmin) {
                return null;
            }

            if ($user->hasRole(config('constants.doctor_role_name'))) {
                $clinic = $user->clinics()->first();
            } else {
                $clinic = $user->assignedClinics()->first()
                    ?? ($user->creator ? $user->creator->clinics()->first() : null);
            }

            if ($clinic) {
                session(['active_clinic_id' => $clinic->id]);
                return $clinic->id;
            }
        }

        return null;
    }
}

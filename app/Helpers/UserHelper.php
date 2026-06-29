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
}

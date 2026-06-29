<?php

namespace App\Http\Controllers\Web;

use App\Enums\CommonStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignupRequest;
use App\Models\Role;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * SignupController
 *
 * Handles public self-registration for Doctors and Patients.
 * Creates the user record and assigns the correct role based on user_type.
 * No super-admin or admin panel logic is touched here.
 */
class SignupController extends Controller
{
    /**
     * Show the multi-step registration form.
     */
    public function showForm(): View
    {
        /* Pass UserType options to the view for the "I am a" dropdown */
        $userTypeOptions = UserType::options();

        return view('auth.signup', compact('userTypeOptions'));
    }

    /**
     * Handle form submission — validate, create user, assign role, redirect.
     */
    public function register(SignupRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            /* Build the user data payload */
            $data = [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'phone_no'   => $request->phone_no,
                'password'   => bcrypt($request->password),
                'status'     => CommonStatus::ACTIVE->value,
            ];

            /* Create the user entry in the users table */
            $user = User::create($data);

            /* Resolve role name from the UserType enum value */
            $roleName = match (UserType::from($request->user_type)) {
                UserType::DOCTOR  => config('constants.doctor_role_name'),
                UserType::PATIENT => config('constants.patient_role_name'),
            };

            /* Assign the resolved role using Spatie */
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->assignRole($role);
            }

            DB::commit();

            return redirect()
                ->route('login')
                ->with('success', __('labels.signup_success'));

        } catch (Exception $e) {
            DB::rollBack();

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', __('labels.signup_error'));
        }
    }
}

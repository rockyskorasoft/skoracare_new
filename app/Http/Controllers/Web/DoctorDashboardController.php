<?php

namespace App\Http\Controllers\Web;

use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * DoctorDashboardController
 *
 * Serves the Doctor-specific dashboard view.
 * Minimal controller — no heavy business logic.
 * All data is passed from the authenticated user model directly.
 */
class DoctorDashboardController extends WebController
{
    /**
     * Render the doctor dashboard.
     *
     * Ensures only Doctor-role users can access this view.
     * Super Admin and Admin are not interrupted — they hit the
     * standard admin.dashboard route and never reach here.
     */
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = UserHelper::getLoggedInUser();

        /* Guard: if somehow a non-Doctor reaches this URL, redirect them */
        if (! $user->hasRole(config('constants.doctor_role_name'))) {
            return redirect()->route('admin.dashboard.index');
        }

        /* The view reads clinic data directly from the auth() user relation,
         * so no extra queries are needed here — clean and optimal. */
        return view('doctors.dashboard');
    }
}

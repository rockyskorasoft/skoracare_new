<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            /* Doctors have their own dedicated dashboard */
            if ($user->hasRole(config('constants.doctor_role_name'))) {
                return redirect()->route('admin.doctor.dashboard');
            }

            return redirect()->route('admin.dashboard.index');
        }

        return $next($request);
    }
}

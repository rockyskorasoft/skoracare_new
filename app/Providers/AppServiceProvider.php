<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant 'Super Admin' & 'Admin' roles all permissions across @can checks
        Gate::before(function ($user, $ability) {
            if ($user->hasRole(config('constants.super_admin_role_name')) || $user->hasRole(config('constants.admin_role_name'))) {
                return true;
            }
        });
    }
}


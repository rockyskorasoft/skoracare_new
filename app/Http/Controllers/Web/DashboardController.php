<?php

namespace App\Http\Controllers\Web;

use App\Services\DashboardService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Initialize dashboard controller.
     */
    public function __construct(public DashboardService $dashboardService){}

    /**
     * Show dashboard landing page.
     */
    public function index()
    {
        if (auth()->check() && auth()->user()->hasRole(config('constants.doctor_role_name'))) {
            return redirect()->route('admin.doctor.dashboard');
        }

        $dashboardData = $this->dashboardService->getDashboardData();

        return view('dashboard.index', compact('dashboardData'));
    }
}

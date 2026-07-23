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
     * Show dashboard landing page for all authenticated users.
     */
    public function index()
    {
        $dashboardData = $this->dashboardService->getDashboardData();

        return view('dashboard.index', compact('dashboardData'));
    }
}

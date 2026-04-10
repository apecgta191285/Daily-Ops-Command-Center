<?php

namespace App\Http\Controllers\Management;

use App\Application\Dashboard\Queries\GetDashboardSnapshot;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke(GetDashboardSnapshot $getDashboardSnapshot)
    {
        return view('dashboard', $getDashboardSnapshot()->toViewData());
    }
}

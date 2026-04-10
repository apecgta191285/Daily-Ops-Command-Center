<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\ChecklistRun;
use App\Models\Incident;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $todayRuns = ChecklistRun::query()
            ->whereDate('run_date', today())
            ->count();

        $submittedTodayRuns = ChecklistRun::query()
            ->whereDate('run_date', today())
            ->whereNotNull('submitted_at')
            ->count();

        $completionRate = $todayRuns > 0
            ? (int) round(($submittedTodayRuns / $todayRuns) * 100)
            : 0;

        $incidentCounts = [
            'Open' => Incident::query()->where('status', 'Open')->count(),
            'In Progress' => Incident::query()->where('status', 'In Progress')->count(),
            'Resolved' => Incident::query()->where('status', 'Resolved')->count(),
        ];

        $recentIncidents = Incident::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'title', 'status', 'severity', 'created_at']);

        return view('dashboard', [
            'todayRuns' => $todayRuns,
            'submittedTodayRuns' => $submittedTodayRuns,
            'completionRate' => $completionRate,
            'incidentCounts' => $incidentCounts,
            'recentIncidents' => $recentIncidents,
        ]);
    }
}

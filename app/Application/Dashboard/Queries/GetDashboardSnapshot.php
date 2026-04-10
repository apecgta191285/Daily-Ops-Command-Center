<?php

namespace App\Application\Dashboard\Queries;

use App\Application\Dashboard\Data\DashboardSnapshot;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\ChecklistRun;
use App\Models\Incident;

class GetDashboardSnapshot
{
    public function __invoke(): DashboardSnapshot
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
            IncidentStatus::Open->value => Incident::query()->where('status', IncidentStatus::Open->value)->count(),
            IncidentStatus::InProgress->value => Incident::query()->where('status', IncidentStatus::InProgress->value)->count(),
            IncidentStatus::Resolved->value => Incident::query()->where('status', IncidentStatus::Resolved->value)->count(),
        ];

        $recentIncidents = Incident::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'title', 'status', 'severity', 'created_at']);

        return new DashboardSnapshot(
            todayRuns: $todayRuns,
            submittedTodayRuns: $submittedTodayRuns,
            completionRate: $completionRate,
            incidentCounts: $incidentCounts,
            recentIncidents: $recentIncidents,
        );
    }
}

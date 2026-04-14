<?php

namespace App\Application\Dashboard\Queries;

use App\Application\Dashboard\Data\DashboardSnapshot;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\ChecklistRun;
use App\Models\Incident;
use Illuminate\Support\Facades\Route;

class GetDashboardSnapshot
{
    private const STALE_INCIDENT_DAYS = 2;

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

        $highSeverityUnresolvedCount = Incident::query()
            ->where('severity', 'High')
            ->where('status', '!=', IncidentStatus::Resolved->value)
            ->count();

        $staleUnresolvedCount = Incident::query()
            ->where('status', '!=', IncidentStatus::Resolved->value)
            ->where('created_at', '<=', now()->subDays(self::STALE_INCIDENT_DAYS))
            ->count();

        $attentionItems = [];

        if ($todayRuns === 0) {
            $attentionItems[] = [
                'title' => 'No checklist runs created today',
                'description' => 'Staff have not opened today\'s checklist flow yet, so daily completion cannot be tracked.',
                'count' => 0,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'warning',
            ];
        } elseif ($completionRate < 100) {
            $attentionItems[] = [
                'title' => 'Checklist completion is still in progress',
                'description' => sprintf(
                    '%d of %d checklist runs are submitted today. Follow up if the team should already be finished.',
                    $submittedTodayRuns,
                    $todayRuns,
                ),
                'count' => $todayRuns - $submittedTodayRuns,
                'actionLabel' => null,
                'url' => null,
                'tone' => 'info',
            ];
        }

        if ($highSeverityUnresolvedCount > 0) {
            $attentionItems[] = [
                'title' => 'High severity incidents need attention',
                'description' => 'Open or in-progress incidents with high severity should be reviewed first.',
                'count' => $highSeverityUnresolvedCount,
                'actionLabel' => 'Review high severity incidents',
                'url' => Route::has('incidents.index')
                    ? route('incidents.index', ['unresolved' => 1, 'severity' => 'High'])
                    : null,
                'tone' => 'danger',
            ];
        }

        if ($staleUnresolvedCount > 0) {
            $attentionItems[] = [
                'title' => 'Unresolved incidents are going stale',
                'description' => sprintf(
                    'These incidents have stayed unresolved for at least %d days and may need follow-up.',
                    self::STALE_INCIDENT_DAYS,
                ),
                'count' => $staleUnresolvedCount,
                'actionLabel' => 'Review stale incidents',
                'url' => Route::has('incidents.index')
                    ? route('incidents.index', ['unresolved' => 1, 'stale' => 1])
                    : null,
                'tone' => 'warning',
            ];
        }

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
            attentionItems: $attentionItems,
            recentIncidents: $recentIncidents,
        );
    }
}

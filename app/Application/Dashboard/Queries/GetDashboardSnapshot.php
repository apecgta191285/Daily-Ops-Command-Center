<?php

namespace App\Application\Dashboard\Queries;

use App\Application\Dashboard\Data\DashboardSnapshot;
use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\ChecklistRun;
use App\Models\Incident;
use Illuminate\Support\Facades\Route;

class GetDashboardSnapshot
{
    public function __invoke(): DashboardSnapshot
    {
        $today = today();
        $yesterday = today()->subDay();

        $todayRuns = ChecklistRun::query()
            ->whereDate('run_date', $today)
            ->count();

        $submittedTodayRuns = ChecklistRun::query()
            ->whereDate('run_date', $today)
            ->whereNotNull('submitted_at')
            ->count();

        $yesterdayRuns = ChecklistRun::query()
            ->whereDate('run_date', $yesterday)
            ->count();

        $submittedYesterdayRuns = ChecklistRun::query()
            ->whereDate('run_date', $yesterday)
            ->whereNotNull('submitted_at')
            ->count();

        $completionRate = $todayRuns > 0
            ? (int) round(($submittedTodayRuns / $todayRuns) * 100)
            : 0;

        $yesterdayCompletionRate = $yesterdayRuns > 0
            ? (int) round(($submittedYesterdayRuns / $yesterdayRuns) * 100)
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

        $staleUnresolvedCount = IncidentStalePolicy::applyToUnresolvedQuery(Incident::query())->count();

        $todayIncidentIntake = Incident::query()
            ->whereDate('created_at', $today)
            ->count();

        $yesterdayIncidentIntake = Incident::query()
            ->whereDate('created_at', $yesterday)
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
                    IncidentStalePolicy::thresholdDays(),
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

        $hotspotCategories = Incident::query()
            ->selectRaw('category, COUNT(*) as unresolved_count')
            ->selectRaw(
                'SUM(CASE WHEN created_at <= ? THEN 1 ELSE 0 END) as stale_count',
                [IncidentStalePolicy::cutoff()->toDateTimeString()],
            )
            ->where('status', '!=', IncidentStatus::Resolved->value)
            ->groupBy('category')
            ->orderByDesc('unresolved_count')
            ->orderBy('category')
            ->limit(3)
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'unresolvedCount' => (int) $row->unresolved_count,
                'staleCount' => (int) $row->stale_count,
                'url' => Route::has('incidents.index')
                    ? route('incidents.index', ['unresolved' => 1, 'category' => $row->category])
                    : null,
            ])
            ->all();

        return new DashboardSnapshot(
            todayRuns: $todayRuns,
            submittedTodayRuns: $submittedTodayRuns,
            completionRate: $completionRate,
            incidentCounts: $incidentCounts,
            attentionItems: $attentionItems,
            checklistTrend: $this->buildChecklistTrend($completionRate, $yesterdayCompletionRate),
            incidentIntakeTrend: $this->buildIncidentIntakeTrend($todayIncidentIntake, $yesterdayIncidentIntake),
            hotspotCategories: $hotspotCategories,
            recentIncidents: $recentIncidents,
        );
    }

    /**
     * @return array{
     *     todayRate: int,
     *     yesterdayRate: int,
     *     difference: int,
     *     direction: 'up'|'down'|'flat'
     * }
     */
    private function buildChecklistTrend(int $todayRate, int $yesterdayRate): array
    {
        $difference = $todayRate - $yesterdayRate;

        return [
            'todayRate' => $todayRate,
            'yesterdayRate' => $yesterdayRate,
            'difference' => abs($difference),
            'direction' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'flat'),
        ];
    }

    /**
     * @return array{
     *     todayCount: int,
     *     yesterdayCount: int,
     *     difference: int,
     *     direction: 'up'|'down'|'flat'
     * }
     */
    private function buildIncidentIntakeTrend(int $todayCount, int $yesterdayCount): array
    {
        $difference = $todayCount - $yesterdayCount;

        return [
            'todayCount' => $todayCount,
            'yesterdayCount' => $yesterdayCount,
            'difference' => abs($difference),
            'direction' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'flat'),
        ];
    }
}

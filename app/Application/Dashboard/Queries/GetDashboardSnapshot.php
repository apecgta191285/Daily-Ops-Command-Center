<?php

namespace App\Application\Dashboard\Queries;

use App\Application\Dashboard\Data\DashboardSnapshot;
use App\Application\Dashboard\Support\DashboardAttentionAssembler;
use App\Application\Dashboard\Support\DashboardHotspotAssembler;
use App\Application\Dashboard\Support\DashboardTrendBuilder;
use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\ChecklistRun;
use App\Models\Incident;

class GetDashboardSnapshot
{
    public function __construct(
        private readonly DashboardAttentionAssembler $attentionAssembler,
        private readonly DashboardTrendBuilder $trendBuilder,
        private readonly DashboardHotspotAssembler $hotspotAssembler,
    ) {}

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

        $attentionItems = ($this->attentionAssembler)(
            todayRuns: $todayRuns,
            submittedTodayRuns: $submittedTodayRuns,
            completionRate: $completionRate,
            highSeverityUnresolvedCount: $highSeverityUnresolvedCount,
            staleUnresolvedCount: $staleUnresolvedCount,
        );

        $recentIncidents = Incident::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'title', 'status', 'severity', 'created_at']);

        $hotspotRows = Incident::query()
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
            ->get();

        return new DashboardSnapshot(
            todayRuns: $todayRuns,
            submittedTodayRuns: $submittedTodayRuns,
            completionRate: $completionRate,
            incidentCounts: $incidentCounts,
            attentionItems: $attentionItems,
            checklistTrend: $this->trendBuilder->buildRateTrend($completionRate, $yesterdayCompletionRate),
            incidentIntakeTrend: $this->trendBuilder->buildCountTrend($todayIncidentIntake, $yesterdayIncidentIntake),
            hotspotCategories: ($this->hotspotAssembler)($hotspotRows),
            recentIncidents: $recentIncidents,
        );
    }
}

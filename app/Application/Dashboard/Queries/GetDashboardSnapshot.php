<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Queries;

use App\Application\Dashboard\Data\DashboardSnapshot;
use App\Application\Dashboard\Support\DashboardAttentionAssembler;
use App\Application\Dashboard\Support\DashboardHotspotAssembler;
use App\Application\Dashboard\Support\DashboardOwnershipBucketBuilder;
use App\Application\Dashboard\Support\DashboardOwnershipPressureBuilder;
use App\Application\Dashboard\Support\DashboardScopeLaneBuilder;
use App\Application\Dashboard\Support\DashboardTrendBuilder;
use App\Application\Dashboard\Support\DashboardWorkboardBuilder;
use App\Application\Incidents\Support\IncidentFollowUpPolicy;
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
        private readonly DashboardScopeLaneBuilder $scopeLaneBuilder,
        private readonly DashboardOwnershipBucketBuilder $ownershipBucketBuilder,
        private readonly DashboardOwnershipPressureBuilder $ownershipPressureBuilder,
        private readonly DashboardWorkboardBuilder $workboardBuilder,
    ) {}

    public function __invoke(?int $actorId = null): DashboardSnapshot
    {
        $today = today();
        $yesterday = today()->subDay();
        $checklistCompletionSeries = $this->buildChecklistCompletionSeries();
        $incidentIntakeSeries = $this->buildIncidentIntakeSeries();
        $scopeChecklistLanes = ($this->scopeLaneBuilder)();

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
        $unownedUnresolvedCount = Incident::query()
            ->where('status', '!=', IncidentStatus::Resolved->value)
            ->whereNull('owner_id')
            ->count();
        $overdueFollowUpCount = IncidentFollowUpPolicy::applyOverdueToUnresolvedQuery(Incident::query())->count();
        $ownedByActorCount = $actorId !== null
            ? Incident::query()
                ->where('status', '!=', IncidentStatus::Resolved->value)
                ->where('owner_id', $actorId)
                ->count()
            : 0;

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
            unownedUnresolvedCount: $unownedUnresolvedCount,
            overdueFollowUpCount: $overdueFollowUpCount,
            scopeLanesMissingTemplateCount: collect($scopeChecklistLanes)->where('state', 'unavailable')->count(),
            scopeLanesIncompleteCount: collect($scopeChecklistLanes)
                ->filter(fn (array $lane): bool => in_array($lane['state'], ['not_started', 'in_progress'], true))
                ->count(),
        );

        $ownershipPressure = ($this->ownershipPressureBuilder)(
            unownedCount: $unownedUnresolvedCount,
            overdueCount: $overdueFollowUpCount,
            ownedByActorCount: $ownedByActorCount,
        );

        $ownershipBuckets = ($this->ownershipBucketBuilder)(
            unownedCount: $unownedUnresolvedCount,
            overdueCount: $overdueFollowUpCount,
            ownedByActorCount: $ownedByActorCount,
        );

        $workboard = ($this->workboardBuilder)(
            scopeChecklistLanes: $scopeChecklistLanes,
            attentionItems: $attentionItems,
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
            checklistTrend: [
                ...$this->trendBuilder->buildRateTrend($completionRate, $yesterdayCompletionRate),
                'series' => $checklistCompletionSeries,
            ],
            incidentIntakeTrend: [
                ...$this->trendBuilder->buildCountTrend($todayIncidentIntake, $yesterdayIncidentIntake),
                'series' => $incidentIntakeSeries,
            ],
            hotspotCategories: ($this->hotspotAssembler)($hotspotRows),
            scopeChecklistLanes: $scopeChecklistLanes,
            workboard: $workboard,
            ownershipBuckets: $ownershipBuckets,
            ownershipPressure: $ownershipPressure,
            recentIncidents: $recentIncidents,
        );
    }

    /**
     * @return list<int>
     */
    private function buildChecklistCompletionSeries(int $days = 7): array
    {
        $startDate = today()->subDays($days - 1);

        $rows = ChecklistRun::query()
            ->selectRaw('DATE(run_date) as run_day')
            ->selectRaw('COUNT(*) as total_runs')
            ->selectRaw('SUM(CASE WHEN submitted_at IS NOT NULL THEN 1 ELSE 0 END) as submitted_runs')
            ->whereDate('run_date', '>=', $startDate)
            ->groupBy('run_day')
            ->orderBy('run_day')
            ->get()
            ->keyBy('run_day');

        $series = [];

        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $date = today()->subDays($offset)->toDateString();
            $row = $rows->get($date);
            $totalRuns = (int) ($row->total_runs ?? 0);
            $submittedRuns = (int) ($row->submitted_runs ?? 0);

            $series[] = $totalRuns > 0
                ? (int) round(($submittedRuns / $totalRuns) * 100)
                : 0;
        }

        return $series;
    }

    /**
     * @return list<int>
     */
    private function buildIncidentIntakeSeries(int $days = 7): array
    {
        $startDate = today()->subDays($days - 1);

        $rows = Incident::query()
            ->selectRaw('DATE(created_at) as report_day')
            ->selectRaw('COUNT(*) as reported_count')
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('report_day')
            ->orderBy('report_day')
            ->get()
            ->keyBy('report_day');

        $series = [];

        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $date = today()->subDays($offset)->toDateString();
            $row = $rows->get($date);

            $series[] = (int) ($row->reported_count ?? 0);
        }

        return $series;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Queries;

use App\Application\Checklists\Support\ChecklistRunArchiveContextBuilder;
use App\Application\Dashboard\Data\DashboardSnapshot;
use App\Application\Dashboard\Support\DashboardAttentionAssembler;
use App\Application\Dashboard\Support\DashboardHotspotAssembler;
use App\Application\Dashboard\Support\DashboardIncidentSummaryBuilder;
use App\Application\Dashboard\Support\DashboardOwnershipBucketBuilder;
use App\Application\Dashboard\Support\DashboardOwnershipPressureBuilder;
use App\Application\Dashboard\Support\DashboardRecentHistoryContextBuilder;
use App\Application\Dashboard\Support\DashboardScopeLaneBuilder;
use App\Application\Dashboard\Support\DashboardTrendBuilder;
use App\Application\Dashboard\Support\DashboardWorkboardBuilder;
use App\Application\Incidents\Queries\ListIncidentHistorySlices;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\ChecklistRun;
use App\Models\Incident;
use Carbon\CarbonInterface;

class GetDashboardSnapshot
{
    public function __construct(
        private readonly DashboardAttentionAssembler $attentionAssembler,
        private readonly DashboardTrendBuilder $trendBuilder,
        private readonly DashboardHotspotAssembler $hotspotAssembler,
        private readonly DashboardIncidentSummaryBuilder $incidentSummaryBuilder,
        private readonly DashboardScopeLaneBuilder $scopeLaneBuilder,
        private readonly DashboardOwnershipBucketBuilder $ownershipBucketBuilder,
        private readonly DashboardOwnershipPressureBuilder $ownershipPressureBuilder,
        private readonly DashboardRecentHistoryContextBuilder $recentHistoryContextBuilder,
        private readonly DashboardWorkboardBuilder $workboardBuilder,
        private readonly ChecklistRunArchiveContextBuilder $checklistArchiveContextBuilder,
        private readonly ListIncidentHistorySlices $listIncidentHistorySlices,
    ) {}

    public function __invoke(?int $actorId = null): DashboardSnapshot
    {
        $today = today()->startOfDay();
        $yesterday = $today->copy()->subDay();
        $checklistRunSummary = $this->buildChecklistRunSummary($today, $yesterday);
        $checklistCompletionSeries = $this->buildChecklistCompletionSeries();
        $incidentIntakeSeries = $this->buildIncidentIntakeSeries();
        $scopeChecklistLanes = ($this->scopeLaneBuilder)();
        $incidentSummary = ($this->incidentSummaryBuilder)($today, $yesterday, $actorId);

        $todayRuns = $checklistRunSummary['todayRuns'];
        $submittedTodayRuns = $checklistRunSummary['submittedTodayRuns'];
        $yesterdayRuns = $checklistRunSummary['yesterdayRuns'];
        $submittedYesterdayRuns = $checklistRunSummary['submittedYesterdayRuns'];

        $completionRate = $todayRuns > 0
            ? (int) round(($submittedTodayRuns / $todayRuns) * 100)
            : 0;

        $yesterdayCompletionRate = $yesterdayRuns > 0
            ? (int) round(($submittedYesterdayRuns / $yesterdayRuns) * 100)
            : 0;

        $incidentCounts = [
            IncidentStatus::Open->value => $incidentSummary['openCount'],
            IncidentStatus::InProgress->value => $incidentSummary['inProgressCount'],
            IncidentStatus::Resolved->value => $incidentSummary['resolvedCount'],
        ];

        $highSeverityUnresolvedCount = $incidentSummary['highSeverityUnresolvedCount'];
        $staleUnresolvedCount = $incidentSummary['staleUnresolvedCount'];
        $unownedUnresolvedCount = $incidentSummary['unownedUnresolvedCount'];
        $overdueFollowUpCount = $incidentSummary['overdueFollowUpCount'];
        $ownedByActorCount = $incidentSummary['ownedByActorCount'];
        $todayIncidentIntake = $incidentSummary['todayIncidentIntake'];
        $yesterdayIncidentIntake = $incidentSummary['yesterdayIncidentIntake'];

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

        $recentArchiveRuns = ChecklistRun::query()
            ->whereNotNull('submitted_at')
            ->where('run_date', '<', $today->toDateString())
            ->where('run_date', '>=', $today->copy()->subDays(6)->toDateString())
            ->with(['template', 'creator', 'submitter'])
            ->withCount([
                'items',
                'items as not_done_items_count' => fn ($query) => $query->where('result', ChecklistResult::NotDone->value),
                'items as noted_items_count' => fn ($query) => $query->whereNotNull('note'),
            ])
            ->orderByDesc('run_date')
            ->orderByDesc('submitted_at')
            ->get();

        $recentArchiveFocusDate = $recentArchiveRuns->first()?->run_date?->toDateString();

        $recentHistoryContext = ($this->recentHistoryContextBuilder)(
            archiveContext: ($this->checklistArchiveContextBuilder)(
                $recentArchiveRuns,
                $recentArchiveFocusDate,
            ),
            incidentHistory: ($this->listIncidentHistorySlices)(7),
        );

        $recentIncidents = Incident::query()
            ->with('room')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'title', 'status', 'severity', 'room_id', 'equipment_reference', 'created_at']);

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
            hotspotCategories: ($this->hotspotAssembler)(($this->incidentSummaryBuilder)->hotspotRows()),
            scopeChecklistLanes: $scopeChecklistLanes,
            workboard: $workboard,
            ownershipBuckets: $ownershipBuckets,
            recentHistoryContext: $recentHistoryContext,
            ownershipPressure: $ownershipPressure,
            recentIncidents: $recentIncidents,
        );
    }

    /**
     * @return array{
     *     todayRuns: int,
     *     submittedTodayRuns: int,
     *     yesterdayRuns: int,
     *     submittedYesterdayRuns: int
     * }
     */
    private function buildChecklistRunSummary(CarbonInterface $today, CarbonInterface $yesterday): array
    {
        $todayDate = $today->toDateString();
        $yesterdayDate = $yesterday->toDateString();
        $tomorrowDate = $today->copy()->addDay()->toDateString();

        $summary = ChecklistRun::query()
            ->selectRaw(
                'SUM(CASE WHEN run_date >= ? AND run_date < ? THEN 1 ELSE 0 END) as today_runs',
                [$todayDate, $tomorrowDate],
            )
            ->selectRaw(
                'SUM(CASE WHEN run_date >= ? AND run_date < ? AND submitted_at IS NOT NULL THEN 1 ELSE 0 END) as submitted_today_runs',
                [$todayDate, $tomorrowDate],
            )
            ->selectRaw(
                'SUM(CASE WHEN run_date >= ? AND run_date < ? THEN 1 ELSE 0 END) as yesterday_runs',
                [$yesterdayDate, $todayDate],
            )
            ->selectRaw(
                'SUM(CASE WHEN run_date >= ? AND run_date < ? AND submitted_at IS NOT NULL THEN 1 ELSE 0 END) as submitted_yesterday_runs',
                [$yesterdayDate, $todayDate],
            )
            ->first();

        return [
            'todayRuns' => (int) ($summary?->today_runs ?? 0),
            'submittedTodayRuns' => (int) ($summary?->submitted_today_runs ?? 0),
            'yesterdayRuns' => (int) ($summary?->yesterday_runs ?? 0),
            'submittedYesterdayRuns' => (int) ($summary?->submitted_yesterday_runs ?? 0),
        ];
    }

    /**
     * @return list<int>
     */
    private function buildChecklistCompletionSeries(int $days = 7): array
    {
        $startDate = today()->subDays($days - 1)->toDateString();
        $endDate = today()->addDay()->toDateString();

        $rows = ChecklistRun::query()
            ->selectRaw('DATE(run_date) as run_day')
            ->selectRaw('COUNT(*) as total_runs')
            ->selectRaw('SUM(CASE WHEN submitted_at IS NOT NULL THEN 1 ELSE 0 END) as submitted_runs')
            ->where('run_date', '>=', $startDate)
            ->where('run_date', '<', $endDate)
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
        $startDate = today()->subDays($days - 1)->startOfDay()->toDateTimeString();

        $rows = Incident::query()
            ->selectRaw('DATE(created_at) as report_day')
            ->selectRaw('COUNT(*) as reported_count')
            ->where('created_at', '>=', $startDate)
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

<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Queries;

use App\Application\Checklists\Support\ChecklistRunArchiveContextBuilder;
use App\Application\Dashboard\Data\DashboardSnapshot;
use App\Application\Dashboard\Support\DashboardAttentionAssembler;
use App\Application\Dashboard\Support\DashboardHotspotAssembler;
use App\Application\Dashboard\Support\DashboardOwnershipBucketBuilder;
use App\Application\Dashboard\Support\DashboardOwnershipPressureBuilder;
use App\Application\Dashboard\Support\DashboardRecentHistoryContextBuilder;
use App\Application\Dashboard\Support\DashboardScopeLaneBuilder;
use App\Application\Dashboard\Support\DashboardTrendBuilder;
use App\Application\Dashboard\Support\DashboardWorkboardBuilder;
use App\Application\Incidents\Queries\ListIncidentHistorySlices;
use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Incidents\Enums\IncidentSeverity;
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
        $today = today();
        $yesterday = today()->subDay();
        $checklistCompletionSeries = $this->buildChecklistCompletionSeries();
        $incidentIntakeSeries = $this->buildIncidentIntakeSeries();
        $scopeChecklistLanes = ($this->scopeLaneBuilder)();
        $incidentSummary = $this->buildIncidentSummary($today, $yesterday, $actorId);

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
            ->whereDate('run_date', '<', $today)
            ->whereDate('run_date', '>=', $today->copy()->subDays(6))
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
            recentHistoryContext: $recentHistoryContext,
            ownershipPressure: $ownershipPressure,
            recentIncidents: $recentIncidents,
        );
    }

    /**
     * @return array{
     *     openCount: int,
     *     inProgressCount: int,
     *     resolvedCount: int,
     *     highSeverityUnresolvedCount: int,
     *     staleUnresolvedCount: int,
     *     unownedUnresolvedCount: int,
     *     overdueFollowUpCount: int,
     *     ownedByActorCount: int,
     *     todayIncidentIntake: int,
     *     yesterdayIncidentIntake: int
     * }
     */
    private function buildIncidentSummary(CarbonInterface $today, CarbonInterface $yesterday, ?int $actorId): array
    {
        $resolved = IncidentStatus::Resolved->value;
        $open = IncidentStatus::Open->value;
        $inProgress = IncidentStatus::InProgress->value;
        $highSeverity = IncidentSeverity::High->value;
        $staleCutoff = IncidentStalePolicy::cutoff()->toDateTimeString();
        $followUpCutoff = $today->toDateString();
        $todayDate = $today->toDateString();
        $yesterdayDate = $yesterday->toDateString();

        $summary = Incident::query()
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as open_count',
                [$open],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as in_progress_count',
                [$inProgress],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as resolved_count',
                [$resolved],
            )
            ->selectRaw(
                'SUM(CASE WHEN severity = ? AND status != ? THEN 1 ELSE 0 END) as high_severity_unresolved_count',
                [$highSeverity, $resolved],
            )
            ->selectRaw(
                'SUM(CASE WHEN status != ? AND created_at <= ? THEN 1 ELSE 0 END) as stale_unresolved_count',
                [$resolved, $staleCutoff],
            )
            ->selectRaw(
                'SUM(CASE WHEN status != ? AND owner_id IS NULL THEN 1 ELSE 0 END) as unowned_unresolved_count',
                [$resolved],
            )
            ->selectRaw(
                'SUM(CASE WHEN status != ? AND follow_up_due_at IS NOT NULL AND DATE(follow_up_due_at) < ? THEN 1 ELSE 0 END) as overdue_follow_up_count',
                [$resolved, $followUpCutoff],
            )
            ->selectRaw(
                'SUM(CASE WHEN status != ? AND owner_id = ? THEN 1 ELSE 0 END) as owned_by_actor_count',
                [$resolved, $actorId ?? 0],
            )
            ->selectRaw(
                'SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as today_incident_intake',
                [$todayDate],
            )
            ->selectRaw(
                'SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as yesterday_incident_intake',
                [$yesterdayDate],
            )
            ->first();

        return [
            'openCount' => (int) ($summary?->open_count ?? 0),
            'inProgressCount' => (int) ($summary?->in_progress_count ?? 0),
            'resolvedCount' => (int) ($summary?->resolved_count ?? 0),
            'highSeverityUnresolvedCount' => (int) ($summary?->high_severity_unresolved_count ?? 0),
            'staleUnresolvedCount' => (int) ($summary?->stale_unresolved_count ?? 0),
            'unownedUnresolvedCount' => (int) ($summary?->unowned_unresolved_count ?? 0),
            'overdueFollowUpCount' => (int) ($summary?->overdue_follow_up_count ?? 0),
            'ownedByActorCount' => $actorId !== null ? (int) ($summary?->owned_by_actor_count ?? 0) : 0,
            'todayIncidentIntake' => (int) ($summary?->today_incident_intake ?? 0),
            'yesterdayIncidentIntake' => (int) ($summary?->yesterday_incident_intake ?? 0),
        ];
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

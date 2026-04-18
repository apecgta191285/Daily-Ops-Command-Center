<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Data;

use App\Models\Incident;
use Illuminate\Support\Collection;

readonly class DashboardSnapshot
{
    /**
     * @param  array<string, int>  $incidentCounts
     * @param  list<array{
     *     title: string,
     *     description: string,
     *     count: int,
     *     actionLabel: string|null,
     *     url: string|null,
     *     tone: 'warning'|'danger'|'info'
     * }>  $attentionItems
     * @param  array{
     *     todayRate: int,
     *     yesterdayRate: int,
     *     difference: int,
     *     direction: 'up'|'down'|'flat',
     *     series: list<int>
     * }  $checklistTrend
     * @param  array{
     *     todayCount: int,
     *     yesterdayCount: int,
     *     difference: int,
     *     direction: 'up'|'down'|'flat',
     *     series: list<int>
     * }  $incidentIntakeTrend
     * @param  list<array{
     *     category: string,
     *     unresolvedCount: int,
     *     staleCount: int,
     *     url: string|null
     * }>  $hotspotCategories
     * @param  list<array{
     *     scope: string,
     *     scope_key: string,
     *     template_title: ?string,
     *     state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *     total_runs: int,
     *     submitted_runs: int,
     *     completion_percentage: int
     * }>  $scopeChecklistLanes
     * @param  Collection<int, Incident>  $recentIncidents
     */
    public function __construct(
        public int $todayRuns,
        public int $submittedTodayRuns,
        public int $completionRate,
        public array $incidentCounts,
        public array $attentionItems,
        public array $checklistTrend,
        public array $incidentIntakeTrend,
        public array $hotspotCategories,
        public array $scopeChecklistLanes,
        public Collection $recentIncidents,
    ) {}

    /**
     * @return array{
     *     todayRuns: int,
     *     submittedTodayRuns: int,
     *     completionRate: int,
     *     incidentCounts: array<string, int>,
     *     attentionItems: list<array{
     *         title: string,
     *         description: string,
     *         count: int,
     *         actionLabel: string|null,
     *         url: string|null,
     *         tone: 'warning'|'danger'|'info'
     *     }>,
     *     checklistTrend: array{
     *         todayRate: int,
     *         yesterdayRate: int,
     *         difference: int,
     *         direction: 'up'|'down'|'flat',
     *         series: list<int>
     *     },
     *     incidentIntakeTrend: array{
     *         todayCount: int,
     *         yesterdayCount: int,
     *         difference: int,
     *         direction: 'up'|'down'|'flat',
     *         series: list<int>
     *     },
     *     hotspotCategories: list<array{
     *         category: string,
     *         unresolvedCount: int,
     *         staleCount: int,
     *         url: string|null
     *     }>,
     *     scopeChecklistLanes: list<array{
     *         scope: string,
     *         scope_key: string,
     *         template_title: ?string,
     *         state: 'unavailable'|'not_started'|'in_progress'|'submitted',
     *         total_runs: int,
     *         submitted_runs: int,
     *         completion_percentage: int
     *     }>,
     *     recentIncidents: Collection<int, Incident>
     * }
     */
    public function toViewData(): array
    {
        return [
            'todayRuns' => $this->todayRuns,
            'submittedTodayRuns' => $this->submittedTodayRuns,
            'completionRate' => $this->completionRate,
            'incidentCounts' => $this->incidentCounts,
            'attentionItems' => $this->attentionItems,
            'checklistTrend' => $this->checklistTrend,
            'incidentIntakeTrend' => $this->incidentIntakeTrend,
            'hotspotCategories' => $this->hotspotCategories,
            'scopeChecklistLanes' => $this->scopeChecklistLanes,
            'recentIncidents' => $this->recentIncidents,
        ];
    }
}

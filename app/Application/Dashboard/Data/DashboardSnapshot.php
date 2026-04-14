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
     * @param  Collection<int, Incident>  $recentIncidents
     */
    public function __construct(
        public int $todayRuns,
        public int $submittedTodayRuns,
        public int $completionRate,
        public array $incidentCounts,
        public array $attentionItems,
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
            'recentIncidents' => $this->recentIncidents,
        ];
    }
}

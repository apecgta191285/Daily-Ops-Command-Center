<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Data;

use App\Models\Incident;
use Illuminate\Support\Collection;

readonly class DashboardSnapshot
{
    /**
     * @param  array<string, int>  $incidentCounts
     * @param  Collection<int, Incident>  $recentIncidents
     */
    public function __construct(
        public int $todayRuns,
        public int $submittedTodayRuns,
        public int $completionRate,
        public array $incidentCounts,
        public Collection $recentIncidents,
    ) {}

    /**
     * @return array{
     *     todayRuns: int,
     *     submittedTodayRuns: int,
     *     completionRate: int,
     *     incidentCounts: array<string, int>,
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
            'recentIncidents' => $this->recentIncidents,
        ];
    }
}

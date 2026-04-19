<?php

declare(strict_types=1);

namespace App\Application\Checklists\Support;

use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistRun;
use Illuminate\Support\Collection;

class ChecklistRunArchiveContextBuilder
{
    /**
     * @param  Collection<int, ChecklistRun>  $runs
     * @return array{
     *   focus_date:?string,
     *   total_runs:int,
     *   total_not_done_items:int,
     *   total_noted_items:int,
     *   lanes:array<int, array{scope:string, state:string, submitted_count:int, operator_names:array<int,string>}>
     * }
     */
    public function __invoke(Collection $runs, ?string $preferredDate = null): array
    {
        $focusDate = $preferredDate;

        if ($focusDate === null || $focusDate === '') {
            $focusDate = $runs->first()?->run_date?->toDateString();
        }

        if ($focusDate === null) {
            return [
                'focus_date' => null,
                'total_runs' => 0,
                'total_not_done_items' => 0,
                'total_noted_items' => 0,
                'lanes' => [],
            ];
        }

        $dateRuns = $runs
            ->filter(fn ($run) => $run->run_date?->toDateString() === $focusDate)
            ->values();

        return [
            'focus_date' => $focusDate,
            'total_runs' => $dateRuns->count(),
            'total_not_done_items' => (int) $dateRuns->sum('not_done_items_count'),
            'total_noted_items' => (int) $dateRuns->sum('noted_items_count'),
            'lanes' => collect(ChecklistScope::cases())
                ->map(function (ChecklistScope $scope) use ($dateRuns) {
                    $laneRuns = $dateRuns
                        ->filter(fn ($run) => $run->assigned_team_or_scope === $scope->value)
                        ->values();

                    return [
                        'scope' => $scope->value,
                        'state' => $laneRuns->isEmpty() ? 'warning' : 'covered',
                        'submitted_count' => $laneRuns->count(),
                        'operator_names' => $laneRuns
                            ->map(fn ($run) => $run->creator?->name)
                            ->filter()
                            ->unique()
                            ->values()
                            ->all(),
                    ];
                })
                ->all(),
        ];
    }
}

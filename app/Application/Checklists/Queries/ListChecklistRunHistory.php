<?php

declare(strict_types=1);

namespace App\Application\Checklists\Queries;

use App\Application\Checklists\Data\ChecklistRunHistoryFilters;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistRun;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class ListChecklistRunHistory
{
    /**
     * @return Collection<int, ChecklistRun>
     */
    public function __invoke(ChecklistRunHistoryFilters $filters): Collection
    {
        $scope = ChecklistScope::fromRouteKey($filters->scopeRouteKey);
        $operatorId = is_numeric($filters->operatorId) ? (int) $filters->operatorId : null;
        $runDate = $filters->normalizedRunDate();
        $nextRunDate = $runDate !== ''
            ? CarbonImmutable::parse($runDate)->addDay()->toDateString()
            : null;

        return ChecklistRun::query()
            ->whereNotNull('submitted_at')
            ->when($runDate !== '', fn ($query) => $query
                ->where('run_date', '>=', $runDate)
                ->where('run_date', '<', $nextRunDate))
            ->when($scope !== null, fn ($query) => $query->where('assigned_team_or_scope', $scope->value))
            ->when($operatorId !== null, fn ($query) => $query->where('created_by', $operatorId))
            ->with(['template', 'room', 'creator', 'submitter'])
            ->withCount([
                'items',
                'items as not_done_items_count' => fn ($query) => $query->where('result', ChecklistResult::NotDone->value),
                'items as noted_items_count' => fn ($query) => $query->whereNotNull('note'),
            ])
            ->orderByDesc('run_date')
            ->orderByDesc('submitted_at')
            ->get();
    }
}

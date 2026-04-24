<?php

declare(strict_types=1);

namespace App\Application\Checklists\Queries;

use App\Application\Checklists\Data\ChecklistRunHistoryFilters;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistRun;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ListChecklistRunHistory
{
    /**
     * @return Collection<int, ChecklistRun>
     */
    public function __invoke(ChecklistRunHistoryFilters $filters): Collection
    {
        /** @var Collection<int, ChecklistRun> $runs */
        $runs = $this->query($filters)->get();

        return $runs;
    }

    public function paginate(ChecklistRunHistoryFilters $filters, int $perPage = 15, string $pageName = 'page'): LengthAwarePaginator
    {
        return $this->query($filters)->paginate($perPage, ['*'], $pageName);
    }

    /**
     * @return Collection<int, ChecklistRun>
     */
    public function focusDateRuns(ChecklistRunHistoryFilters $filters, ?string $focusDate): Collection
    {
        if ($focusDate === null || $focusDate === '') {
            return new Collection;
        }

        return $this->query($filters, $focusDate)->get();
    }

    public function query(ChecklistRunHistoryFilters $filters, ?string $forcedRunDate = null): Builder
    {
        $scope = ChecklistScope::fromRouteKey($filters->scopeRouteKey);
        $operatorId = is_numeric($filters->operatorId) ? (int) $filters->operatorId : null;
        $runDate = $forcedRunDate ?? $filters->normalizedRunDate();
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
            ->orderByDesc('id');
    }
}

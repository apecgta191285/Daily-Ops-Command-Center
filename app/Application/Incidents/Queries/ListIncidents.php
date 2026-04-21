<?php

declare(strict_types=1);

namespace App\Application\Incidents\Queries;

use App\Application\Incidents\Data\IncidentListFilters;
use App\Application\Incidents\Support\IncidentFollowUpPolicy;
use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class ListIncidents
{
    public function __invoke(IncidentListFilters $filters): Collection
    {
        /** @var Collection<int, Incident> $incidents */
        $incidents = $this->query($filters)->get();

        return $this->decorateCollection($incidents);
    }

    public function paginate(IncidentListFilters $filters, int $perPage = 15, string $pageName = 'page'): LengthAwarePaginator
    {
        $paginator = $this->query($filters)->paginate($perPage, ['*'], $pageName);

        $paginator->setCollection($this->decorateCollection($paginator->getCollection()));

        return $paginator;
    }

    public function query(IncidentListFilters $filters): Builder
    {
        $status = $filters->statusEnum();
        $category = $filters->categoryEnum();
        $severity = $filters->severityEnum();

        return Incident::query()
            ->with(['creator', 'owner'])
            ->when($filters->unresolved, fn ($query) => $query->where('status', '!=', IncidentStatus::Resolved->value))
            ->when($filters->stale, fn ($query) => IncidentStalePolicy::applyToUnresolvedQuery($query))
            ->when($filters->unowned, fn ($query) => $query
                ->where('status', '!=', IncidentStatus::Resolved->value)
                ->whereNull('owner_id'))
            ->when($filters->mine && $filters->actorId !== null, fn ($query) => $query
                ->where('status', '!=', IncidentStatus::Resolved->value)
                ->where('owner_id', $filters->actorId))
            ->when($filters->overdue, fn ($query) => IncidentFollowUpPolicy::applyOverdueToUnresolvedQuery($query))
            ->when($status !== null, fn ($query) => $query->where('status', $status->value))
            ->when($category !== null, fn ($query) => $query->where('category', $category->value))
            ->when($severity !== null, fn ($query) => $query->where('severity', $severity->value))
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /**
     * @param  SupportCollection<int, Incident>  $incidents
     * @return SupportCollection<int, Incident>
     */
    private function decorateCollection(SupportCollection $incidents): SupportCollection
    {
        return $incidents->each(function (Incident $incident): void {
            $incident->setAttribute(
                'is_stale_for_attention',
                IncidentStalePolicy::isStale($incident->created_at, $incident->status),
            );
            $incident->setAttribute(
                'is_overdue_follow_up',
                IncidentFollowUpPolicy::isOverdue($incident->follow_up_due_at, $incident->status),
            );
        });
    }
}

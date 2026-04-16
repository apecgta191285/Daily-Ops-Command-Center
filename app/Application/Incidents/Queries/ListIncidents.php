<?php

namespace App\Application\Incidents\Queries;

use App\Application\Incidents\Data\IncidentListFilters;
use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Illuminate\Database\Eloquent\Collection;

class ListIncidents
{
    public function __invoke(IncidentListFilters $filters): Collection
    {
        $incidents = Incident::query()
            ->with('creator')
            ->when($filters->unresolved, fn ($query) => $query->where('status', '!=', IncidentStatus::Resolved->value))
            ->when($filters->stale, fn ($query) => IncidentStalePolicy::applyToUnresolvedQuery($query))
            ->when($filters->status !== '', fn ($query) => $query->where('status', $filters->status))
            ->when($filters->category !== '', fn ($query) => $query->where('category', $filters->category))
            ->when($filters->severity !== '', fn ($query) => $query->where('severity', $filters->severity))
            ->latest()
            ->get();

        return $incidents->each(function (Incident $incident): void {
            $incident->setAttribute(
                'is_stale_for_attention',
                IncidentStalePolicy::isStale($incident->created_at, $incident->status),
            );
        });
    }
}

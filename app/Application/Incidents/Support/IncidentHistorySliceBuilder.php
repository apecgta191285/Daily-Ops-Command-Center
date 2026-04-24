<?php

declare(strict_types=1);

namespace App\Application\Incidents\Support;

use App\Models\Incident;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class IncidentHistorySliceBuilder
{
    /**
     * @param  Collection<int, Incident>  $openedIncidents
     * @param  Collection<int, Incident>  $resolvedIncidents
     * @return array{
     *   days:int,
     *   start_date:string,
     *   end_date:string,
     *   opened_count:int,
     *   resolved_count:int,
     *   still_active_count:int,
     *   slices:array<int, array{
     *     date:string,
     *     label:string,
     *     opened_count:int,
     *     resolved_count:int,
     *     still_active_count:int,
     *     opened:array<int, array{id:int,title:string,severity:string,status:string,owner_name:?string,creator_name:?string,room_name:?string,equipment_reference:?string,url:string}>,
     *     resolved:array<int, array{id:int,title:string,severity:string,status:string,owner_name:?string,creator_name:?string,room_name:?string,equipment_reference:?string,url:string}>
     *   }>
     * }
     */
    public function __invoke(
        Collection $openedIncidents,
        Collection $resolvedIncidents,
        CarbonImmutable $startDate,
        CarbonImmutable $endDate,
        int $days,
    ): array {
        $openedByDate = $openedIncidents
            ->groupBy(fn (Incident $incident): ?string => $incident->created_at?->toDateString());

        $resolvedByDate = $resolvedIncidents
            ->groupBy(fn (Incident $incident): ?string => $incident->resolved_at?->toDateString());

        $slices = collect(range(0, $days - 1))
            ->map(function (int $offset) use ($openedByDate, $resolvedByDate, $startDate): array {
                $date = $startDate->addDays($offset);
                $dateKey = $date->toDateString();

                /** @var Collection<int, Incident> $opened */
                $opened = $openedByDate->get($dateKey, collect())->values();

                /** @var Collection<int, Incident> $resolved */
                $resolved = $resolvedByDate->get($dateKey, collect())->values();

                return [
                    'date' => $dateKey,
                    'label' => $date->format('M d'),
                    'opened_count' => $opened->count(),
                    'resolved_count' => $resolved->count(),
                    'still_active_count' => $opened->whereNull('resolved_at')->count(),
                    'opened' => $opened
                        ->map(fn (Incident $incident): array => $this->mapIncident($incident))
                        ->all(),
                    'resolved' => $resolved
                        ->map(fn (Incident $incident): array => $this->mapIncident($incident))
                        ->all(),
                ];
            })
            ->filter(fn (array $slice): bool => $slice['opened_count'] > 0 || $slice['resolved_count'] > 0)
            ->values();

        return [
            'days' => $days,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'opened_count' => (int) $slices->sum('opened_count'),
            'resolved_count' => (int) $slices->sum('resolved_count'),
            'still_active_count' => (int) $slices->sum('still_active_count'),
            'slices' => $slices->all(),
        ];
    }

    /**
     * @return array{id:int,title:string,severity:string,status:string,owner_name:?string,creator_name:?string,room_name:?string,equipment_reference:?string,url:string}
     */
    private function mapIncident(Incident $incident): array
    {
        return [
            'id' => $incident->id,
            'title' => $incident->title,
            'severity' => $incident->severity->value,
            'status' => $incident->status->value,
            'owner_name' => $incident->owner?->name,
            'creator_name' => $incident->creator?->name,
            'room_name' => $incident->room?->name,
            'equipment_reference' => $incident->equipment_reference,
            'url' => route('incidents.show', $incident),
        ];
    }
}

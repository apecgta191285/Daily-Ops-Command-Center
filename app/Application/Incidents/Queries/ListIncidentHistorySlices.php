<?php

declare(strict_types=1);

namespace App\Application\Incidents\Queries;

use App\Application\Incidents\Support\IncidentHistorySliceBuilder;
use App\Models\Incident;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ListIncidentHistorySlices
{
    /**
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
     *     opened:array<int, array{id:int,title:string,severity:string,status:string,owner_name:?string,creator_name:?string,url:string}>,
     *     resolved:array<int, array{id:int,title:string,severity:string,status:string,owner_name:?string,creator_name:?string,url:string}>
     *   }>
     * }
     */
    public function __invoke(int $days = 7): array
    {
        $days = in_array($days, [7, 14, 30], true) ? $days : 7;

        $startDate = CarbonImmutable::today()->subDays($days - 1)->startOfDay();
        $endDate = CarbonImmutable::today()->endOfDay();

        return app(IncidentHistorySliceBuilder::class)(
            $this->openedIncidents($startDate, $endDate),
            $this->resolvedIncidents($startDate, $endDate),
            $startDate,
            $endDate,
            $days,
        );
    }

    /**
     * @return Collection<int, Incident>
     */
    protected function openedIncidents(CarbonImmutable $startDate, CarbonImmutable $endDate): Collection
    {
        return Incident::query()
            ->with(['creator', 'owner', 'room'])
            ->whereBetween('created_at', [
                $startDate->toDateTimeString(),
                $endDate->toDateTimeString(),
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @return Collection<int, Incident>
     */
    protected function resolvedIncidents(CarbonImmutable $startDate, CarbonImmutable $endDate): Collection
    {
        return Incident::query()
            ->with(['creator', 'owner', 'room'])
            ->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [
                $startDate->toDateTimeString(),
                $endDate->toDateTimeString(),
            ])
            ->orderByDesc('resolved_at')
            ->orderByDesc('id')
            ->get();
    }
}

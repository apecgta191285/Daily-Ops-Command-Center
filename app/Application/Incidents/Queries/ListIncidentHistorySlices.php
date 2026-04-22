<?php

declare(strict_types=1);

namespace App\Application\Incidents\Queries;

use App\Application\Incidents\Support\IncidentHistorySliceBuilder;
use App\Models\Incident;
use Carbon\CarbonImmutable;

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

        $incidents = Incident::query()
            ->with(['creator', 'owner', 'room'])
            ->where(function ($query) use ($startDate): void {
                $query
                    ->whereDate('created_at', '>=', $startDate->toDateString())
                    ->orWhereDate('resolved_at', '>=', $startDate->toDateString());
            })
            ->latest('created_at')
            ->get();

        return app(IncidentHistorySliceBuilder::class)(
            $incidents,
            $startDate,
            $endDate,
            $days,
        );
    }
}

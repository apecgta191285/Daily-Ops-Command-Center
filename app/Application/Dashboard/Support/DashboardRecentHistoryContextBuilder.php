<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use Illuminate\Support\Facades\Route;

class DashboardRecentHistoryContextBuilder
{
    /**
     * @param  array{
     *   focus_date:?string,
     *   total_runs:int,
     *   total_not_done_items:int,
     *   total_noted_items:int,
     *   lanes:array<int, array{scope:string, state:string, submitted_count:int, operator_names:array<int,string>}>
     * }  $archiveContext
     * @param  array{
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
     * }  $incidentHistory
     * @return array{
     *   state:'calm'|'watch'|'unstable',
     *   headline:string,
     *   body:string,
     *   archive:array{
     *     focus_date:?string,
     *     total_runs:int,
     *     total_not_done_items:int,
     *     total_noted_items:int,
     *     covered_lanes:int,
     *     warning_lanes:int,
     *     url:string|null
     *   },
     *   incidents:array{
     *     days:int,
     *     opened_count:int,
     *     resolved_count:int,
     *     still_active_count:int,
     *     url:string|null
     *   }
     * }
     */
    public function __invoke(array $archiveContext, array $incidentHistory): array
    {
        $warningLanes = collect($archiveContext['lanes'])->where('state', 'warning')->count();
        $coveredLanes = collect($archiveContext['lanes'])->where('state', 'covered')->count();

        $state = match (true) {
            ($archiveContext['total_not_done_items'] ?? 0) > 0 || ($incidentHistory['still_active_count'] ?? 0) > 0 => 'unstable',
            ($archiveContext['total_noted_items'] ?? 0) > 0 || ($incidentHistory['opened_count'] ?? 0) > ($incidentHistory['resolved_count'] ?? 0) => 'watch',
            default => 'calm',
        };

        return [
            'state' => $state,
            'headline' => $this->headline($state),
            'body' => $this->body($state),
            'archive' => [
                'focus_date' => $archiveContext['focus_date'],
                'total_runs' => $archiveContext['total_runs'],
                'total_not_done_items' => $archiveContext['total_not_done_items'],
                'total_noted_items' => $archiveContext['total_noted_items'],
                'covered_lanes' => $coveredLanes,
                'warning_lanes' => $warningLanes,
                'url' => $archiveContext['focus_date'] !== null
                    ? $this->routeOrNull('checklists.history.index', ['runDate' => $archiveContext['focus_date']])
                    : $this->routeOrNull('checklists.history.index'),
            ],
            'incidents' => [
                'days' => $incidentHistory['days'],
                'opened_count' => $incidentHistory['opened_count'],
                'resolved_count' => $incidentHistory['resolved_count'],
                'still_active_count' => $incidentHistory['still_active_count'],
                'url' => $this->routeOrNull('incidents.history.index', ['days' => $incidentHistory['days']]),
            ],
        ];
    }

    private function headline(string $state): string
    {
        return match ($state) {
            'unstable' => 'Recent operating record still shows carryover',
            'watch' => 'Recent operating record needs a light review',
            default => 'Recent operating record looks settled',
        };
    }

    private function body(string $state): string
    {
        return match ($state) {
            'unstable' => 'Use the recent archive and incident history to confirm whether today is inheriting unfinished work rather than only reacting to current counts.',
            'watch' => 'The recent record is not alarming, but there are enough notes or new inflow signals to justify a quick review before calling the day stable.',
            default => 'Recent archive and incident slices do not show meaningful carryover pressure right now. Today can be managed from the live board first.',
        };
    }

    /**
     * @param  array<string, int|string>  $parameters
     */
    private function routeOrNull(string $name, array $parameters = []): ?string
    {
        return Route::has($name) ? route($name, $parameters) : null;
    }
}

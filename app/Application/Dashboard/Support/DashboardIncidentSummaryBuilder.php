<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class DashboardIncidentSummaryBuilder
{
    /**
     * @return array{
     *     openCount:int,
     *     inProgressCount:int,
     *     resolvedCount:int,
     *     highSeverityUnresolvedCount:int,
     *     staleUnresolvedCount:int,
     *     unownedUnresolvedCount:int,
     *     overdueFollowUpCount:int,
     *     ownedByActorCount:int,
     *     todayIncidentIntake:int,
     *     yesterdayIncidentIntake:int
     * }
     */
    public function __invoke(CarbonInterface $today, CarbonInterface $yesterday, ?int $actorId): array
    {
        $statusCounts = $this->statusCounts();
        $unresolvedPressure = $this->unresolvedPressureCounts($today, $actorId);
        $intakeCounts = $this->intakeCounts($today, $yesterday);

        return [
            'openCount' => (int) ($statusCounts[IncidentStatus::Open->value] ?? 0),
            'inProgressCount' => (int) ($statusCounts[IncidentStatus::InProgress->value] ?? 0),
            'resolvedCount' => (int) ($statusCounts[IncidentStatus::Resolved->value] ?? 0),
            'highSeverityUnresolvedCount' => (int) ($unresolvedPressure['highSeverityUnresolvedCount'] ?? 0),
            'staleUnresolvedCount' => (int) ($unresolvedPressure['staleUnresolvedCount'] ?? 0),
            'unownedUnresolvedCount' => (int) ($unresolvedPressure['unownedUnresolvedCount'] ?? 0),
            'overdueFollowUpCount' => (int) ($unresolvedPressure['overdueFollowUpCount'] ?? 0),
            'ownedByActorCount' => (int) ($unresolvedPressure['ownedByActorCount'] ?? 0),
            'todayIncidentIntake' => (int) ($intakeCounts['todayIncidentIntake'] ?? 0),
            'yesterdayIncidentIntake' => (int) ($intakeCounts['yesterdayIncidentIntake'] ?? 0),
        ];
    }

    /**
     * @return array<string, int>
     */
    protected function statusCounts(): array
    {
        return Incident::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn (mixed $count): int => (int) $count)
            ->all();
    }

    /**
     * @return array{
     *     highSeverityUnresolvedCount:int,
     *     staleUnresolvedCount:int,
     *     unownedUnresolvedCount:int,
     *     overdueFollowUpCount:int,
     *     ownedByActorCount:int
     * }
     */
    protected function unresolvedPressureCounts(CarbonInterface $today, ?int $actorId): array
    {
        $resolved = IncidentStatus::Resolved->value;

        $summary = Incident::query()
            ->where('status', '!=', $resolved)
            ->selectRaw(
                'SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) as high_severity_unresolved_count',
                [IncidentSeverity::High->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN created_at <= ? THEN 1 ELSE 0 END) as stale_unresolved_count',
                [IncidentStalePolicy::cutoff()->toDateTimeString()],
            )
            ->selectRaw('SUM(CASE WHEN owner_id IS NULL THEN 1 ELSE 0 END) as unowned_unresolved_count')
            ->selectRaw(
                'SUM(CASE WHEN follow_up_due_at IS NOT NULL AND follow_up_due_at < ? THEN 1 ELSE 0 END) as overdue_follow_up_count',
                [$today->toDateString()],
            )
            ->selectRaw(
                'SUM(CASE WHEN owner_id = ? THEN 1 ELSE 0 END) as owned_by_actor_count',
                [$actorId ?? 0],
            )
            ->first();

        return [
            'highSeverityUnresolvedCount' => (int) ($summary?->high_severity_unresolved_count ?? 0),
            'staleUnresolvedCount' => (int) ($summary?->stale_unresolved_count ?? 0),
            'unownedUnresolvedCount' => (int) ($summary?->unowned_unresolved_count ?? 0),
            'overdueFollowUpCount' => (int) ($summary?->overdue_follow_up_count ?? 0),
            'ownedByActorCount' => $actorId !== null ? (int) ($summary?->owned_by_actor_count ?? 0) : 0,
        ];
    }

    /**
     * @return array{todayIncidentIntake:int,yesterdayIncidentIntake:int}
     */
    protected function intakeCounts(CarbonInterface $today, CarbonInterface $yesterday): array
    {
        $rows = Incident::query()
            ->selectRaw('DATE(created_at) as report_day')
            ->selectRaw('COUNT(*) as reported_count')
            ->where('created_at', '>=', $yesterday->copy()->startOfDay()->toDateTimeString())
            ->where('created_at', '<', $today->copy()->addDay()->startOfDay()->toDateTimeString())
            ->groupBy('report_day')
            ->pluck('reported_count', 'report_day');

        return [
            'todayIncidentIntake' => (int) ($rows[$today->toDateString()] ?? 0),
            'yesterdayIncidentIntake' => (int) ($rows[$yesterday->toDateString()] ?? 0),
        ];
    }

    /**
     * @return Collection<int, object>
     */
    public function hotspotRows(): Collection
    {
        return Incident::query()
            ->select('category')
            ->selectRaw('COUNT(*) as unresolved_count')
            ->selectRaw(
                'SUM(CASE WHEN created_at <= ? THEN 1 ELSE 0 END) as stale_count',
                [IncidentStalePolicy::cutoff()->toDateTimeString()],
            )
            ->where('status', '!=', IncidentStatus::Resolved->value)
            ->groupBy('category')
            ->orderByDesc('unresolved_count')
            ->orderBy('category')
            ->limit(3)
            ->get();
    }
}

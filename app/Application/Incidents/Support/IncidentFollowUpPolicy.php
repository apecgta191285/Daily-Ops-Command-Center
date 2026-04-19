<?php

declare(strict_types=1);

namespace App\Application\Incidents\Support;

use App\Domain\Incidents\Enums\IncidentStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class IncidentFollowUpPolicy
{
    public static function isOverdue(?CarbonInterface $followUpDueAt, string $status, ?CarbonInterface $today = null): bool
    {
        if ($followUpDueAt === null || $status === IncidentStatus::Resolved->value) {
            return false;
        }

        return $followUpDueAt->startOfDay()->lt(($today ?? today())->startOfDay());
    }

    public static function applyOverdueToUnresolvedQuery(Builder $query, ?CarbonInterface $today = null): Builder
    {
        return $query
            ->where('status', '!=', IncidentStatus::Resolved->value)
            ->whereDate('follow_up_due_at', '<', ($today ?? today())->toDateString());
    }
}

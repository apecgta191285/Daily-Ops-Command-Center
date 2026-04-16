<?php

namespace App\Application\Incidents\Support;

use App\Domain\Incidents\Enums\IncidentStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class IncidentStalePolicy
{
    private const THRESHOLD_DAYS = 2;

    public static function thresholdDays(): int
    {
        return self::THRESHOLD_DAYS;
    }

    public static function cutoff(?CarbonInterface $now = null): CarbonInterface
    {
        return ($now ?? now())->copy()->subDays(self::THRESHOLD_DAYS);
    }

    public static function isStale(CarbonInterface $createdAt, string $status, ?CarbonInterface $now = null): bool
    {
        return $status !== IncidentStatus::Resolved->value
            && $createdAt->lte(self::cutoff($now));
    }

    public static function applyToUnresolvedQuery(Builder $query, ?CarbonInterface $now = null): Builder
    {
        return $query
            ->where('status', '!=', IncidentStatus::Resolved->value)
            ->where('created_at', '<=', self::cutoff($now));
    }
}

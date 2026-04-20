<?php

declare(strict_types=1);

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

    public static function isStale(CarbonInterface $createdAt, IncidentStatus|string $status, ?CarbonInterface $now = null): bool
    {
        $status = $status instanceof IncidentStatus ? $status : IncidentStatus::from($status);

        return $status !== IncidentStatus::Resolved
            && $createdAt->lte(self::cutoff($now));
    }

    public static function applyToUnresolvedQuery(Builder $query, ?CarbonInterface $now = null): Builder
    {
        return $query
            ->where('status', '!=', IncidentStatus::Resolved->value)
            ->where('created_at', '<=', self::cutoff($now));
    }
}

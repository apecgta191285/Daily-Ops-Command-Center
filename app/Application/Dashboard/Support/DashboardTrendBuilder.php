<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Support;

class DashboardTrendBuilder
{
    /**
     * @return array{
     *     todayRate: int,
     *     yesterdayRate: int,
     *     difference: int,
     *     direction: 'up'|'down'|'flat'
     * }
     */
    public function buildRateTrend(int $todayRate, int $yesterdayRate): array
    {
        $comparison = $this->compare($todayRate, $yesterdayRate);

        return [
            'todayRate' => $todayRate,
            'yesterdayRate' => $yesterdayRate,
            'difference' => $comparison['difference'],
            'direction' => $comparison['direction'],
        ];
    }

    /**
     * @return array{
     *     todayCount: int,
     *     yesterdayCount: int,
     *     difference: int,
     *     direction: 'up'|'down'|'flat'
     * }
     */
    public function buildCountTrend(int $todayCount, int $yesterdayCount): array
    {
        $comparison = $this->compare($todayCount, $yesterdayCount);

        return [
            'todayCount' => $todayCount,
            'yesterdayCount' => $yesterdayCount,
            'difference' => $comparison['difference'],
            'direction' => $comparison['direction'],
        ];
    }

    /**
     * @return array{
     *     difference: int,
     *     direction: 'up'|'down'|'flat'
     * }
     */
    private function compare(int $current, int $previous): array
    {
        $difference = $current - $previous;

        return [
            'difference' => abs($difference),
            'direction' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'flat'),
        ];
    }
}

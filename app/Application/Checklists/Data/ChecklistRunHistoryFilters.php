<?php

declare(strict_types=1);

namespace App\Application\Checklists\Data;

use Carbon\CarbonImmutable;

readonly class ChecklistRunHistoryFilters
{
    public function __construct(
        public string $runDate = '',
        public string $scopeRouteKey = '',
        public string $operatorId = '',
    ) {}

    public function normalizedRunDate(): string
    {
        return self::normalizeRunDate($this->runDate);
    }

    public static function normalizeRunDate(string $value): string
    {
        if ($value === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return '';
        }

        try {
            $parsed = CarbonImmutable::createFromFormat('!Y-m-d', $value);
        } catch (\Throwable) {
            return '';
        }

        return $parsed !== false && $parsed->format('Y-m-d') === $value
            ? $value
            : '';
    }
}

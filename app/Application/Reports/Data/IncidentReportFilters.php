<?php

declare(strict_types=1);

namespace App\Application\Reports\Data;

use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Domain\Incidents\Enums\IncidentSubcategory;
use Carbon\CarbonImmutable;

readonly class IncidentReportFilters
{
    public function __construct(
        public CarbonImmutable $startDate,
        public CarbonImmutable $endDate,
        public ?int $roomId = null,
        public string $category = '',
        public string $subcategory = '',
        public string $status = '',
        public string $severity = '',
    ) {}

    public static function forLastDays(int $days = 30): self
    {
        $days = max(1, min($days, 366));

        return new self(
            startDate: CarbonImmutable::today()->subDays($days - 1)->startOfDay(),
            endDate: CarbonImmutable::today()->endOfDay(),
        );
    }

    public function categoryEnum(): ?IncidentCategory
    {
        return IncidentCategory::tryFrom($this->category);
    }

    public function statusEnum(): ?IncidentStatus
    {
        return IncidentStatus::tryFrom($this->status);
    }

    public function severityEnum(): ?IncidentSeverity
    {
        return IncidentSeverity::tryFrom($this->severity);
    }

    public function normalizedSubcategory(): string
    {
        if ($this->subcategory === '') {
            return '';
        }

        return IncidentSubcategory::isValidForCategory($this->subcategory, $this->category !== '' ? $this->category : null)
            ? $this->subcategory
            : '';
    }
}

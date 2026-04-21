<?php

declare(strict_types=1);

namespace App\Application\Incidents\Data;

use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;

readonly class IncidentListFilters
{
    public function __construct(
        public string $status = '',
        public string $category = '',
        public string $severity = '',
        public bool $unresolved = false,
        public bool $stale = false,
        public bool $unowned = false,
        public bool $mine = false,
        public bool $overdue = false,
        public ?int $actorId = null,
    ) {}

    public function statusEnum(): ?IncidentStatus
    {
        return IncidentStatus::tryFrom($this->status);
    }

    public function categoryEnum(): ?IncidentCategory
    {
        return IncidentCategory::tryFrom($this->category);
    }

    public function severityEnum(): ?IncidentSeverity
    {
        return IncidentSeverity::tryFrom($this->severity);
    }
}

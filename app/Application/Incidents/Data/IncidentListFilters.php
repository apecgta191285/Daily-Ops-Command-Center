<?php

namespace App\Application\Incidents\Data;

readonly class IncidentListFilters
{
    public function __construct(
        public string $status = '',
        public string $category = '',
        public string $severity = '',
        public bool $unresolved = false,
        public bool $stale = false,
    ) {}
}

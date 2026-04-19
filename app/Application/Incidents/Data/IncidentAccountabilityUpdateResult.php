<?php

declare(strict_types=1);

namespace App\Application\Incidents\Data;

use App\Models\Incident;

readonly class IncidentAccountabilityUpdateResult
{
    public function __construct(
        public Incident $incident,
        public bool $changed,
    ) {}
}

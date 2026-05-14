<?php

declare(strict_types=1);

namespace App\Domain\Incidents\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched after an incident's status has been transitioned
 * and the corresponding activity log entries have been committed.
 */
readonly class IncidentStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $incidentId,
        public string $previousStatus,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Domain\Incidents\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched after an incident's owner or follow-up due date
 * has been updated and the corresponding activity entries have been committed.
 */
readonly class IncidentAccountabilityChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $incidentId,
    ) {}
}

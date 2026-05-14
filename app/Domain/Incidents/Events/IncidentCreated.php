<?php

declare(strict_types=1);

namespace App\Domain\Incidents\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched after a new incident has been persisted and its initial
 * activity log entry has been committed to the database.
 */
readonly class IncidentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $incidentId,
    ) {}
}

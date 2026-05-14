<?php

declare(strict_types=1);

namespace App\Application\Incidents\Listeners;

use App\Application\Incidents\Support\ExternalIncidentNotifier;
use App\Domain\Incidents\Events\IncidentAccountabilityChanged;
use App\Domain\Incidents\Events\IncidentCreated;
use App\Domain\Incidents\Events\IncidentStatusChanged;
use App\Models\Incident;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Attribute\AsEventListener;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Queued listener that dispatches external notifications (LINE, etc.)
 * whenever an incident lifecycle event occurs.
 *
 * Runs on the queue so that LINE API latency or failures never block
 * the user's HTTP request.  Each attempt is retried up to 3 times with
 * exponential back-off to handle transient network failures gracefully.
 */
#[AsEventListener(event: IncidentCreated::class, method: 'onCreated')]
#[AsEventListener(event: IncidentStatusChanged::class, method: 'onStatusChanged')]
#[AsEventListener(event: IncidentAccountabilityChanged::class, method: 'onAccountabilityChanged')]
class SendExternalNotificationOnIncidentEvent implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [2, 10, 30];

    public function onCreated(IncidentCreated $event): void
    {
        $incident = Incident::with(['room', 'creator'])->find($event->incidentId);

        if ($incident === null) {
            return;
        }

        app(ExternalIncidentNotifier::class)->incidentCreated($incident);
    }

    public function onStatusChanged(IncidentStatusChanged $event): void
    {
        $incident = Incident::with(['room', 'owner'])->find($event->incidentId);

        if ($incident === null) {
            return;
        }

        app(ExternalIncidentNotifier::class)->statusChanged($incident, $event->previousStatus);
    }

    public function onAccountabilityChanged(IncidentAccountabilityChanged $event): void
    {
        $incident = Incident::with(['room', 'owner'])->find($event->incidentId);

        if ($incident === null) {
            return;
        }

        app(ExternalIncidentNotifier::class)->accountabilityChanged($incident);
    }
}

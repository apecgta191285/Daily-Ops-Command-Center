<?php

namespace App\Application\Incidents\Actions;

use App\Application\Incidents\Data\IncidentStatusTransitionResult;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransitionIncidentStatus
{
    public function __invoke(Incident $incident, string $nextStatus, int $actorId, ?string $nextActionNote = null): IncidentStatusTransitionResult
    {
        if (! in_array($nextStatus, IncidentStatus::values(), true)) {
            throw ValidationException::withMessages([
                'status' => ['Incident status is invalid.'],
            ]);
        }

        $nextActionNote = filled($nextActionNote) ? trim($nextActionNote) : null;

        $previousStatus = $incident->status;

        if ($nextStatus === $previousStatus) {
            return new IncidentStatusTransitionResult(
                incident: $incident->load(['creator', 'activities.actor']),
                changed: false,
                previousStatus: $previousStatus,
            );
        }

        DB::transaction(function () use ($incident, $nextStatus, $previousStatus, $actorId, $nextActionNote): void {
            $incident->update([
                'status' => $nextStatus,
                'resolved_at' => $nextStatus === IncidentStatus::Resolved->value ? now() : null,
            ]);

            $incident->activities()->create([
                'action_type' => 'status_changed',
                'summary' => "Status changed from {$previousStatus} to {$nextStatus}",
                'actor_id' => $actorId,
                'created_at' => now(),
            ]);

            if ($nextActionNote !== null) {
                $incident->activities()->create([
                    'action_type' => 'next_action_note',
                    'summary' => "Next action: {$nextActionNote}",
                    'actor_id' => $actorId,
                    'created_at' => now(),
                ]);
            }
        });

        return new IncidentStatusTransitionResult(
            incident: $incident->fresh(['creator', 'activities.actor']),
            changed: true,
            previousStatus: $previousStatus,
        );
    }
}

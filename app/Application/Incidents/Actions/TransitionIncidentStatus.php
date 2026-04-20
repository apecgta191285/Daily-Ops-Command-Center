<?php

declare(strict_types=1);

namespace App\Application\Incidents\Actions;

use App\Application\Incidents\Data\IncidentStatusTransitionResult;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransitionIncidentStatus
{
    public function __invoke(Incident $incident, string $nextStatus, int $actorId, ?string $followUpNote = null): IncidentStatusTransitionResult
    {
        if (! in_array($nextStatus, IncidentStatus::values(), true)) {
            throw ValidationException::withMessages([
                'status' => ['Incident status is invalid.'],
            ]);
        }

        $followUpNote = filled($followUpNote) ? trim($followUpNote) : null;

        $previousStatus = $incident->status->value;

        if ($nextStatus === $previousStatus) {
            return new IncidentStatusTransitionResult(
                incident: $incident->load(['creator', 'owner', 'activities.actor']),
                changed: false,
                previousStatus: $previousStatus,
            );
        }

        DB::transaction(function () use ($incident, $nextStatus, $previousStatus, $actorId, $followUpNote): void {
            $nextStatusEnum = IncidentStatus::from($nextStatus);

            $incident->update([
                'status' => $nextStatusEnum,
                'resolved_at' => $nextStatusEnum === IncidentStatus::Resolved ? now() : null,
            ]);

            $incident->activities()->create([
                'action_type' => 'status_changed',
                'summary' => "Status changed from {$previousStatus} to {$nextStatus}",
                'actor_id' => $actorId,
                'created_at' => now(),
            ]);

            if ($followUpNote !== null) {
                $isResolutionNote = $nextStatusEnum === IncidentStatus::Resolved;

                $incident->activities()->create([
                    'action_type' => $isResolutionNote ? 'resolution_note' : 'next_action_note',
                    'summary' => $isResolutionNote
                        ? "Resolution: {$followUpNote}"
                        : "Next action: {$followUpNote}",
                    'actor_id' => $actorId,
                    'created_at' => now(),
                ]);
            }
        });

        return new IncidentStatusTransitionResult(
            incident: $incident->fresh(['creator', 'owner', 'activities.actor']),
            changed: true,
            previousStatus: $previousStatus,
        );
    }
}

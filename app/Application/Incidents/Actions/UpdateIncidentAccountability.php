<?php

declare(strict_types=1);

namespace App\Application\Incidents\Actions;

use App\Application\Incidents\Data\IncidentAccountabilityUpdateResult;
use App\Domain\Access\Enums\UserRole;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateIncidentAccountability
{
    public function __invoke(Incident $incident, ?int $ownerId, ?string $followUpDueAt, int $actorId): IncidentAccountabilityUpdateResult
    {
        $owner = $this->resolveOwner($ownerId);
        $normalizedDueDate = $this->normalizeDueDate($followUpDueAt);

        $currentOwnerId = $incident->owner_id;
        $currentDueDate = $incident->follow_up_due_at?->toDateString();

        $ownerChanged = $currentOwnerId !== $owner?->id;
        $dueDateChanged = $currentDueDate !== $normalizedDueDate;

        if (! $ownerChanged && ! $dueDateChanged) {
            return new IncidentAccountabilityUpdateResult(
                incident: $incident->load(['creator', 'owner', 'activities.actor']),
                changed: false,
            );
        }

        DB::transaction(function () use ($incident, $owner, $normalizedDueDate, $ownerChanged, $dueDateChanged, $actorId): void {
            $previousOwner = $incident->owner;
            $previousDueDate = $incident->follow_up_due_at?->toDateString();

            $incident->update([
                'owner_id' => $owner?->id,
                'follow_up_due_at' => $normalizedDueDate,
            ]);

            if ($ownerChanged) {
                $incident->activities()->create([
                    'action_type' => 'owner_changed',
                    'summary' => $this->ownerSummary($previousOwner?->name, $owner?->name),
                    'actor_id' => $actorId,
                    'created_at' => now(),
                ]);
            }

            if ($dueDateChanged) {
                $incident->activities()->create([
                    'action_type' => 'follow_up_due_at_changed',
                    'summary' => $this->followUpDueDateSummary($previousDueDate, $normalizedDueDate),
                    'actor_id' => $actorId,
                    'created_at' => now(),
                ]);
            }
        });

        return new IncidentAccountabilityUpdateResult(
            incident: $incident->fresh(['creator', 'owner', 'activities.actor']),
            changed: true,
        );
    }

    private function resolveOwner(?int $ownerId): ?User
    {
        if ($ownerId === null) {
            return null;
        }

        $owner = User::query()->find($ownerId);

        if ($owner === null || ! in_array($owner->role, [UserRole::Admin, UserRole::Supervisor], true)) {
            throw ValidationException::withMessages([
                'ownerId' => ['Incident owner must be an administrator or supervisor.'],
            ]);
        }

        return $owner;
    }

    private function normalizeDueDate(?string $followUpDueAt): ?string
    {
        if (! filled($followUpDueAt)) {
            return null;
        }

        try {
            return Carbon::parse($followUpDueAt)->toDateString();
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'followUpDueAt' => ['Follow-up target date is invalid.'],
            ]);
        }
    }

    private function ownerSummary(?string $previousOwnerName, ?string $nextOwnerName): string
    {
        return match (true) {
            $previousOwnerName === null && $nextOwnerName !== null => "Owner assigned to {$nextOwnerName}",
            $previousOwnerName !== null && $nextOwnerName === null => "Owner cleared from {$previousOwnerName}",
            default => "Owner reassigned from {$previousOwnerName} to {$nextOwnerName}",
        };
    }

    private function followUpDueDateSummary(?string $previousDate, ?string $nextDate): string
    {
        $formattedPrevious = $previousDate !== null ? Carbon::parse($previousDate)->format('M d, Y') : null;
        $formattedNext = $nextDate !== null ? Carbon::parse($nextDate)->format('M d, Y') : null;

        return match (true) {
            $formattedPrevious === null && $formattedNext !== null => "Follow-up target set for {$formattedNext}",
            $formattedPrevious !== null && $formattedNext === null => "Follow-up target cleared (was {$formattedPrevious})",
            default => "Follow-up target changed from {$formattedPrevious} to {$formattedNext}",
        };
    }
}

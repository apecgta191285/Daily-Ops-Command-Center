<?php

declare(strict_types=1);

namespace App\Application\Incidents\Actions;

use App\Application\Incidents\Data\IncidentStatusTransitionResult;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Domain\Incidents\Events\IncidentStatusChanged;
use App\Models\Incident;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransitionIncidentStatus
{
    /** @var array<string, list<string>> */
    private const ALLOWED_TRANSITIONS = [
        IncidentStatus::Open->value => [
            IncidentStatus::InProgress->value,
            IncidentStatus::Resolved->value,
        ],
        IncidentStatus::InProgress->value => [
            IncidentStatus::Open->value,
            IncidentStatus::Resolved->value,
        ],
        IncidentStatus::Resolved->value => [
            IncidentStatus::Open->value,
        ],
    ];

    public function __invoke(Incident $incident, string $nextStatus, int $actorId, ?string $followUpNote = null): IncidentStatusTransitionResult
    {
        if (! in_array($nextStatus, IncidentStatus::values(), true)) {
            throw ValidationException::withMessages([
                'status' => ['สถานะรายงานปัญหาไม่ถูกต้อง'],
            ]);
        }

        $followUpNote = filled($followUpNote) ? trim($followUpNote) : null;

        $transition = DB::transaction(function () use ($incident, $nextStatus, $actorId, $followUpNote): array {
            /** @var Incident $lockedIncident */
            $lockedIncident = Incident::query()
                ->whereKey($incident->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $previousStatus = $lockedIncident->status->value;

            if ($nextStatus === $previousStatus) {
                return [
                    'incident' => $lockedIncident,
                    'changed' => false,
                    'previous_status' => $previousStatus,
                ];
            }

            $this->ensureAllowedTransition($previousStatus, $nextStatus);

            $nextStatusEnum = IncidentStatus::from($nextStatus);
            $previousStatusLabel = $this->statusLabel($previousStatus);
            $nextStatusLabel = $this->statusLabel($nextStatus);

            $lockedIncident->update([
                'status' => $nextStatusEnum,
                'resolved_at' => $nextStatusEnum === IncidentStatus::Resolved ? now() : null,
            ]);

            $lockedIncident->activities()->create([
                'action_type' => 'status_changed',
                'summary' => "เปลี่ยนสถานะจาก {$previousStatusLabel} เป็น {$nextStatusLabel}",
                'actor_id' => $actorId,
                'created_at' => now(),
            ]);

            if ($followUpNote !== null) {
                $isResolutionNote = $nextStatusEnum === IncidentStatus::Resolved;

                $lockedIncident->activities()->create([
                    'action_type' => $isResolutionNote ? 'resolution_note' : 'next_action_note',
                    'summary' => $isResolutionNote
                        ? "สรุปการแก้ไข: {$followUpNote}"
                        : "การดำเนินการถัดไป: {$followUpNote}",
                    'actor_id' => $actorId,
                    'created_at' => now(),
                ]);
            }

            return [
                'incident' => $lockedIncident,
                'changed' => true,
                'previous_status' => $previousStatus,
            ];
        });

        /** @var Incident $transitionIncident */
        $transitionIncident = $transition['incident'];
        $freshIncident = $transitionIncident->fresh(['creator', 'owner', 'room', 'activities.actor']);

        if ($transition['changed'] === true) {
            IncidentStatusChanged::dispatch($freshIncident->id, $transition['previous_status']);
        }

        return new IncidentStatusTransitionResult(
            incident: $freshIncident,
            changed: (bool) $transition['changed'],
            previousStatus: (string) $transition['previous_status'],
        );
    }

    private function ensureAllowedTransition(string $previousStatus, string $nextStatus): void
    {
        $allowedTransitions = self::ALLOWED_TRANSITIONS[$previousStatus] ?? [];

        if (in_array($nextStatus, $allowedTransitions, true)) {
            return;
        }

        throw ValidationException::withMessages([
            'status' => ["ไม่สามารถเปลี่ยนสถานะจาก {$this->statusLabel($previousStatus)} เป็น {$this->statusLabel($nextStatus)}"],
        ]);
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            IncidentStatus::Open->value => 'เปิดใหม่',
            IncidentStatus::InProgress->value => 'กำลังดำเนินการ',
            IncidentStatus::Resolved->value => 'แก้ไขแล้ว',
            default => $status,
        };
    }
}

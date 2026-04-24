<?php

declare(strict_types=1);

namespace App\Application\Incidents\Actions;

use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Room;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateIncident
{
    /**
     * @param  array{title: string, category: string, severity: string, description: string, room_id?: int|null, equipment_reference?: string|null}  $payload
     */
    public function __invoke(array $payload, int $actorId, ?UploadedFile $attachment = null): Incident
    {
        if (! in_array($payload['category'], IncidentCategory::values(), true)) {
            throw ValidationException::withMessages([
                'category' => ['Incident category is invalid.'],
            ]);
        }

        if (! in_array($payload['severity'], IncidentSeverity::values(), true)) {
            throw ValidationException::withMessages([
                'severity' => ['Incident severity is invalid.'],
            ]);
        }

        $roomId = isset($payload['room_id']) ? (int) $payload['room_id'] : null;

        if ($roomId === null || $roomId <= 0) {
            throw ValidationException::withMessages([
                'room_id' => ['Room is required.'],
            ]);
        }

        if (! Room::query()->whereKey($roomId)->where('is_active', true)->exists()) {
            throw ValidationException::withMessages([
                'room_id' => ['Room is invalid or inactive.'],
            ]);
        }

        $equipmentReference = isset($payload['equipment_reference'])
            ? trim((string) $payload['equipment_reference'])
            : null;

        if ($equipmentReference === '') {
            $equipmentReference = null;
        }

        if ($equipmentReference !== null && mb_strlen($equipmentReference) > 120) {
            throw ValidationException::withMessages([
                'equipment_reference' => ['Equipment reference must not exceed 120 characters.'],
            ]);
        }

        return DB::transaction(function () use ($payload, $actorId, $attachment, $roomId, $equipmentReference): Incident {
            $attachmentPath = $attachment?->store('incidents', 'local');

            $incident = Incident::create([
                'title' => $payload['title'],
                'category' => $payload['category'],
                'severity' => $payload['severity'],
                'room_id' => $roomId,
                'status' => IncidentStatus::Open->value,
                'description' => $payload['description'],
                'equipment_reference' => $equipmentReference,
                'attachment_path' => $attachmentPath,
                'created_by' => $actorId,
            ]);

            $incident->activities()->create([
                'action_type' => 'created',
                'summary' => 'Incident reported',
                'actor_id' => $actorId,
            ]);

            return $incident->load(['creator', 'room', 'activities.actor']);
        });
    }
}

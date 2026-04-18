<?php

declare(strict_types=1);

namespace App\Application\Incidents\Actions;

use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateIncident
{
    /**
     * @param  array{title: string, category: string, severity: string, description: string}  $payload
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

        return DB::transaction(function () use ($payload, $actorId, $attachment): Incident {
            $attachmentPath = $attachment?->store('incidents', 'public');

            $incident = Incident::create([
                'title' => $payload['title'],
                'category' => $payload['category'],
                'severity' => $payload['severity'],
                'status' => IncidentStatus::Open->value,
                'description' => $payload['description'],
                'attachment_path' => $attachmentPath,
                'created_by' => $actorId,
            ]);

            $incident->activities()->create([
                'action_type' => 'created',
                'summary' => 'Incident reported',
                'actor_id' => $actorId,
            ]);

            return $incident->load(['creator', 'activities.actor']);
        });
    }
}

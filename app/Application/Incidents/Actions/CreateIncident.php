<?php

declare(strict_types=1);

namespace App\Application\Incidents\Actions;

use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Domain\Incidents\Enums\IncidentSubcategory;
use App\Domain\Incidents\Events\IncidentCreated;
use App\Models\Incident;
use App\Models\Room;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateIncident
{
    /**
     * @param  array{title: string, category: string, subcategory?: string|null, severity: string, description: string, room_id?: int|null, equipment_reference?: string|null}  $payload
     */
    public function __invoke(array $payload, int $actorId, ?UploadedFile $attachment = null): Incident
    {
        if (! in_array($payload['category'], IncidentCategory::values(), true)) {
            throw ValidationException::withMessages([
                'category' => ['หมวดหมู่รายงานปัญหาไม่ถูกต้อง'],
            ]);
        }

        $subcategory = trim((string) ($payload['subcategory'] ?? ''));

        if ($subcategory === '') {
            throw ValidationException::withMessages([
                'subcategory' => ['กรุณาเลือกหมวดหมู่ย่อยของรายงานปัญหา'],
            ]);
        }

        if (! IncidentSubcategory::isValidForCategory($subcategory, $payload['category'])) {
            throw ValidationException::withMessages([
                'subcategory' => ['หมวดหมู่ย่อยไม่สัมพันธ์กับหมวดหมู่หลักที่เลือก'],
            ]);
        }

        if (! in_array($payload['severity'], IncidentSeverity::values(), true)) {
            throw ValidationException::withMessages([
                'severity' => ['ระดับความรุนแรงของรายงานปัญหาไม่ถูกต้อง'],
            ]);
        }

        $roomId = isset($payload['room_id']) ? (int) $payload['room_id'] : null;

        if ($roomId === null || $roomId <= 0) {
            throw ValidationException::withMessages([
                'room_id' => ['กรุณาเลือกห้อง'],
            ]);
        }

        if (! Room::query()->whereKey($roomId)->where('is_active', true)->exists()) {
            throw ValidationException::withMessages([
                'room_id' => ['ห้องที่เลือกไม่ถูกต้องหรือไม่ได้เปิดใช้งาน'],
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
                'equipment_reference' => ['อุปกรณ์หรือเครื่องที่เกี่ยวข้องต้องยาวไม่เกิน 120 ตัวอักษร'],
            ]);
        }

        $incident = DB::transaction(function () use ($payload, $actorId, $attachment, $roomId, $equipmentReference, $subcategory): Incident {
            $attachmentPath = $attachment?->store('incidents', 'local');

            $incident = Incident::create([
                'title' => $payload['title'],
                'category' => $payload['category'],
                'subcategory' => $subcategory,
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
                'summary' => 'แจ้งรายงานปัญหา',
                'actor_id' => $actorId,
            ]);

            return $incident->load(['creator', 'room', 'activities.actor']);
        });

        IncidentCreated::dispatch($incident->id);

        return $incident;
    }
}

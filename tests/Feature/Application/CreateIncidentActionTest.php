<?php

use App\Application\Incidents\Actions\CreateIncident;
use App\Domain\Access\Enums\UserRole;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->operator = $this->createUserForRole(UserRole::Staff);
});

test('create incident action persists incident attachment and activity log', function () {
    Storage::fake('public');
    $room = $this->createRoom(['name' => 'Lab 1', 'code' => 'LAB-01']);

    $incident = app(CreateIncident::class)([
        'title' => 'Action-created incident',
        'category' => 'เครือข่าย',
        'severity' => 'High',
        'description' => 'Created through application action.',
        'room_id' => $room->id,
        'equipment_reference' => 'PC-12',
    ], $this->operator->id, UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'));

    expect($incident->status)->toBe(IncidentStatus::Open);
    expect($incident->created_by)->toBe($this->operator->id);
    expect($incident->room_id)->toBe($room->id);
    expect($incident->equipment_reference)->toBe('PC-12');
    expect($incident->attachment_path)->not->toBeNull();
    Storage::disk('public')->assertExists($incident->attachment_path);

    $activity = $incident->activities()->first();
    expect($activity->summary)->toBe('Incident reported');
    expect($activity->actor_id)->toBe($this->operator->id);
});

test('create incident action rejects missing room context', function () {
    expect(fn () => app(CreateIncident::class)([
        'title' => 'Missing room incident',
        'category' => 'เครือข่าย',
        'severity' => 'High',
        'description' => 'Created through application action.',
    ], $this->operator->id))->toThrow(ValidationException::class);
});

test('create incident action rejects inactive rooms', function () {
    $room = $this->createRoom(['name' => 'Lab 1', 'code' => 'LAB-01']);
    $room->update(['is_active' => false]);

    expect(fn () => app(CreateIncident::class)([
        'title' => 'Inactive room incident',
        'category' => 'เครือข่าย',
        'severity' => 'High',
        'description' => 'Created through application action.',
        'room_id' => $room->id,
    ], $this->operator->id))->toThrow(ValidationException::class);
});

test('database forbids room-less incidents now that room context is mandatory', function () {
    expect(fn () => Incident::query()->create([
        'title' => 'Roomless incident',
        'category' => IncidentCategory::ComputerEquipment->value,
        'severity' => IncidentSeverity::Medium->value,
        'room_id' => null,
        'status' => IncidentStatus::Open->value,
        'description' => 'This should not be persisted without a room.',
        'created_by' => $this->operator->id,
    ]))->toThrow(QueryException::class);
});

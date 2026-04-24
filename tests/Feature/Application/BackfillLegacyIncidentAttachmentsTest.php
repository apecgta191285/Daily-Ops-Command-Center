<?php

use App\Application\Incidents\Support\BackfillLegacyIncidentAttachments;
use App\Domain\Access\Enums\UserRole;
use App\Models\Incident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');

    $this->operator = $this->createUserForRole(UserRole::Staff);
    $this->room = $this->createRoom([
        'name' => 'Lab Backfill 1',
        'code' => 'LAB-BACKFILL-01',
    ]);
});

test('legacy attachment backfill migrates public evidence to private local storage', function () {
    $incident = Incident::query()->create([
        'title' => 'Legacy public evidence',
        'category' => 'อื่น ๆ',
        'severity' => 'Low',
        'room_id' => $this->room->id,
        'status' => 'Open',
        'description' => 'Needs private-storage backfill.',
        'attachment_path' => 'incidents/legacy-proof.pdf',
        'created_by' => $this->operator->id,
    ]);

    Storage::disk('public')->put($incident->attachment_path, 'legacy-public-proof');

    $summary = app(BackfillLegacyIncidentAttachments::class)();

    expect($summary['scanned'])->toBe(1)
        ->and($summary['migrated'])->toBe(1)
        ->and($summary['already_private'])->toBe(0)
        ->and($summary['missing'])->toBe(0);

    Storage::disk('local')->assertExists($incident->attachment_path);
    Storage::disk('public')->assertMissing($incident->attachment_path);
    expect($incident->fresh()->attachmentDisk())->toBe('local');
});

test('legacy attachment backfill is idempotent for attachments already on the private disk', function () {
    $incident = Incident::query()->create([
        'title' => 'Private evidence',
        'category' => 'อื่น ๆ',
        'severity' => 'Low',
        'room_id' => $this->room->id,
        'status' => 'Open',
        'description' => 'Already stored on the local disk.',
        'attachment_path' => 'incidents/private-proof.pdf',
        'created_by' => $this->operator->id,
    ]);

    Storage::disk('local')->put($incident->attachment_path, 'private-proof');

    $summary = app(BackfillLegacyIncidentAttachments::class)();

    expect($summary['scanned'])->toBe(1)
        ->and($summary['migrated'])->toBe(0)
        ->and($summary['already_private'])->toBe(1)
        ->and($summary['missing'])->toBe(0);
});

test('legacy attachment backfill command reports migration summary and keeps downloads working', function () {
    $incident = Incident::query()->create([
        'title' => 'Legacy command evidence',
        'category' => 'อุปกรณ์คอมพิวเตอร์',
        'severity' => 'Medium',
        'room_id' => $this->room->id,
        'status' => 'Open',
        'description' => 'Attachment is still on the public disk before backfill.',
        'attachment_path' => 'incidents/legacy-command-proof.pdf',
        'created_by' => $this->operator->id,
    ]);

    Storage::disk('public')->put($incident->attachment_path, 'legacy-command-proof');

    $this->artisan('incident-attachments:backfill-private')
        ->expectsOutput('Legacy incident attachment backfill completed.')
        ->assertSuccessful();

    Storage::disk('local')->assertExists($incident->attachment_path);
    Storage::disk('public')->assertMissing($incident->attachment_path);
});

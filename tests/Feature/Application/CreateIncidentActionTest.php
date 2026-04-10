<?php

use App\Application\Incidents\Actions\CreateIncident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->operator = User::where('email', 'operatora@example.com')->firstOrFail();
});

test('create incident action persists incident attachment and activity log', function () {
    Storage::fake('public');

    $incident = app(CreateIncident::class)([
        'title' => 'Action-created incident',
        'category' => 'เครือข่าย',
        'severity' => 'High',
        'description' => 'Created through application action.',
    ], $this->operator->id, UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'));

    expect($incident->status)->toBe('Open');
    expect($incident->created_by)->toBe($this->operator->id);
    expect($incident->attachment_path)->not->toBeNull();
    Storage::disk('public')->assertExists($incident->attachment_path);

    $activity = $incident->activities()->first();
    expect($activity->summary)->toBe('Incident reported');
    expect($activity->actor_id)->toBe($this->operator->id);
});

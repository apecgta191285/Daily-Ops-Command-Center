<?php

use App\Application\Incidents\Actions\TransitionIncidentStatus;
use App\Domain\Access\Enums\UserRole;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->admin = User::where('role', UserRole::Admin->value)->firstOrFail();
    $this->openIncident = Incident::where('status', 'Open')->firstOrFail();
    $this->resolvedIncident = Incident::where('status', 'Resolved')->firstOrFail();
});

test('transition incident status updates incident state and appends activity', function () {
    $result = app(TransitionIncidentStatus::class)($this->openIncident, 'In Progress', $this->admin->id);

    expect($result->changed)->toBeTrue();
    expect($result->incident->status)->toBe('In Progress');
    expect($result->incident->resolved_at)->toBeNull();
    expect($result->incident->activities()->latest('id')->first()->summary)->toBe('Status changed from Open to In Progress');
});

test('transition incident status clears resolved timestamp when reopening', function () {
    $result = app(TransitionIncidentStatus::class)($this->resolvedIncident, 'Open', $this->admin->id);

    expect($result->changed)->toBeTrue();
    expect($result->incident->status)->toBe('Open');
    expect($result->incident->resolved_at)->toBeNull();
});

test('transition incident status returns no-op result when status is unchanged', function () {
    $result = app(TransitionIncidentStatus::class)($this->openIncident, 'Open', $this->admin->id);

    expect($result->changed)->toBeFalse();
});

test('transition incident status can append an optional next action note', function () {
    $result = app(TransitionIncidentStatus::class)(
        $this->openIncident,
        'In Progress',
        $this->admin->id,
        'Follow up with the lab technician before noon.',
    );

    expect($result->changed)->toBeTrue();
    expect($result->incident->activities()->where('action_type', 'next_action_note')->exists())->toBeTrue();
    expect($result->incident->activities()->where('action_type', 'next_action_note')->latest('id')->first()->summary)
        ->toBe('Next action: Follow up with the lab technician before noon.');
});

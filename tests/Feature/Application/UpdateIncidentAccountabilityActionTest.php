<?php

use App\Application\Incidents\Actions\UpdateIncidentAccountability;
use App\Domain\Access\Enums\UserRole;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->admin = User::where('role', UserRole::Admin->value)->firstOrFail();
    $this->supervisor = User::where('role', UserRole::Supervisor->value)->firstOrFail();
    $this->staff = User::where('role', UserRole::Staff->value)->firstOrFail();
    $this->openIncident = Incident::where('status', 'Open')->firstOrFail();
});

test('incident accountability action can assign a management owner and follow-up target', function () {
    $result = app(UpdateIncidentAccountability::class)(
        $this->openIncident,
        $this->supervisor->id,
        '2026-04-21',
        $this->admin->id,
    );

    expect($result->changed)->toBeTrue();
    expect($result->incident->owner_id)->toBe($this->supervisor->id);
    expect($result->incident->follow_up_due_at?->toDateString())->toBe('2026-04-21');
    expect($result->incident->activities()->where('action_type', 'owner_changed')->exists())->toBeTrue();
    expect($result->incident->activities()->where('action_type', 'follow_up_due_at_changed')->exists())->toBeTrue();
});

test('incident accountability action rejects staff as incident owner', function () {
    expect(fn () => app(UpdateIncidentAccountability::class)(
        $this->openIncident,
        $this->staff->id,
        '2026-04-21',
        $this->admin->id,
    ))->toThrow(ValidationException::class);
});

test('incident accountability action can clear owner and follow-up target', function () {
    $this->openIncident->forceFill([
        'owner_id' => $this->admin->id,
        'follow_up_due_at' => '2026-04-21',
    ])->save();

    $result = app(UpdateIncidentAccountability::class)(
        $this->openIncident->fresh(['owner']),
        null,
        null,
        $this->supervisor->id,
    );

    expect($result->changed)->toBeTrue();
    expect($result->incident->owner_id)->toBeNull();
    expect($result->incident->follow_up_due_at)->toBeNull();
});

test('incident accountability action returns no-op result when values are unchanged', function () {
    $result = app(UpdateIncidentAccountability::class)(
        $this->openIncident,
        null,
        null,
        $this->admin->id,
    );

    expect($result->changed)->toBeFalse();
});

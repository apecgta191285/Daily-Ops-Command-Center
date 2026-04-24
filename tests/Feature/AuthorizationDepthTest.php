<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Models\ChecklistTemplate;
use App\Models\Incident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->admin = $this->createUserForRole(UserRole::Admin, [
        'email' => 'policy-admin@example.com',
    ]);
    $this->supervisor = $this->createUserForRole(UserRole::Supervisor, [
        'email' => 'policy-supervisor@example.com',
    ]);
    $this->staff = $this->createUserForRole(UserRole::Staff, [
        'email' => 'policy-staff@example.com',
    ]);
});

test('incident policy allows management users and denies staff for view and update actions', function () {
    $incident = Incident::query()->latest('id')->firstOrFail();

    expect(Gate::forUser($this->admin)->allows('view', $incident))->toBeTrue();
    expect(Gate::forUser($this->admin)->allows('update', $incident))->toBeTrue();
    expect(Gate::forUser($this->supervisor)->allows('view', $incident))->toBeTrue();
    expect(Gate::forUser($this->supervisor)->allows('update', $incident))->toBeTrue();
    expect(Gate::forUser($this->staff)->allows('view', $incident))->toBeFalse();
    expect(Gate::forUser($this->staff)->allows('update', $incident))->toBeFalse();
});

test('checklist run policy allows management users and denies staff for historical recap access', function () {
    $run = $this->createRunForUser(
        $this->staff,
        ChecklistTemplate::query()->orderBy('id')->firstOrFail(),
        submitted: true,
        room: $this->createRoom([
            'name' => 'Policy Lab 1',
            'code' => 'POL-01',
        ]),
    );

    expect(Gate::forUser($this->admin)->allows('view', $run))->toBeTrue();
    expect(Gate::forUser($this->supervisor)->allows('view', $run))->toBeTrue();
    expect(Gate::forUser($this->staff)->allows('view', $run))->toBeFalse();
});

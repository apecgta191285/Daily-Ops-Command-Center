<?php

declare(strict_types=1);

use App\Application\Dashboard\Queries\GetDashboardSnapshot;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistTemplate;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('dashboard snapshot query returns expected metrics and capped recent incidents', function () {
    $snapshot = app(GetDashboardSnapshot::class)();

    expect($snapshot->todayRuns)->toBeGreaterThanOrEqual(0);
    expect($snapshot->submittedTodayRuns)->toBeGreaterThanOrEqual(0);
    expect($snapshot->completionRate)->toBeGreaterThanOrEqual(0);
    expect($snapshot->incidentCounts)->toHaveKeys(['Open', 'In Progress', 'Resolved']);
    expect($snapshot->scopeChecklistLanes)->toHaveCount(count(ChecklistScope::cases()));
    expect($snapshot->recentIncidents->count())->toBeLessThanOrEqual(5);

    $expectedLatest = Incident::query()
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->first();

    expect($snapshot->recentIncidents->first()?->id)->toBe($expectedLatest?->id);
});

test('dashboard snapshot query exposes scope lane states for today', function () {
    $admin = User::factory()->create();

    ChecklistTemplate::query()->update(['is_active' => false]);

    $openingTemplate = $this->createTemplateWithItems([
        'title' => 'Opening snapshot template',
        'scope' => ChecklistScope::OPENING->value,
        'is_active' => true,
    ]);

    $middayTemplate = $this->createTemplateWithItems([
        'title' => 'Midday snapshot template',
        'scope' => ChecklistScope::MIDDAY->value,
        'is_active' => true,
    ]);

    $this->createRunForUser($admin, $openingTemplate, submitted: true);
    $this->createRunForUser($admin, $middayTemplate, submitted: false);

    $snapshot = app(GetDashboardSnapshot::class)();

    $lanes = collect($snapshot->scopeChecklistLanes)->keyBy('scope');

    expect($lanes[ChecklistScope::OPENING->value]['state'])->toBe('submitted');
    expect($lanes[ChecklistScope::OPENING->value]['submitted_runs'])->toBeGreaterThanOrEqual(1);
    expect($lanes[ChecklistScope::MIDDAY->value]['state'])->toBe('in_progress');
    expect($lanes[ChecklistScope::MIDDAY->value]['total_runs'])->toBeGreaterThanOrEqual(1);
    expect($lanes[ChecklistScope::CLOSING->value]['state'])->toBe('unavailable');
});

test('dashboard snapshot query exposes ownership pressure summary', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $supervisor = User::factory()->create(['role' => 'supervisor']);

    Incident::factory()->create([
        'title' => 'Unowned dashboard incident',
        'status' => 'Open',
        'created_by' => $admin->id,
        'owner_id' => null,
    ]);

    Incident::factory()->create([
        'title' => 'Owned by admin dashboard incident',
        'status' => 'In Progress',
        'created_by' => $supervisor->id,
        'owner_id' => $admin->id,
        'follow_up_due_at' => today()->addDay(),
    ]);

    Incident::factory()->create([
        'title' => 'Overdue dashboard incident',
        'status' => 'Open',
        'created_by' => $supervisor->id,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    $snapshot = app(GetDashboardSnapshot::class)($admin->id);

    expect($snapshot->ownershipPressure['unownedCount'])->toBeGreaterThanOrEqual(1);
    expect($snapshot->ownershipPressure['overdueCount'])->toBeGreaterThanOrEqual(1);
    expect($snapshot->ownershipPressure['ownedByActorCount'])->toBeGreaterThanOrEqual(1);
    expect(collect($snapshot->ownershipPressure['actions'])->pluck('label')->all())
        ->toContain('Review unowned incidents', 'Review overdue follow-up', 'Review incidents you own');
    expect($snapshot->ownershipBuckets['state'])->toBe('active');
    expect($snapshot->ownershipBuckets['headline'])->toBe('Follow-up has started slipping past target');
    expect(collect($snapshot->ownershipBuckets['buckets'])->pluck('title')->all())
        ->toBe(['Overdue follow-up', 'Unowned incidents', 'Owned by you']);
});

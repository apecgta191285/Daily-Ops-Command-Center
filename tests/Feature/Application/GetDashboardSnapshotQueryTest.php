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

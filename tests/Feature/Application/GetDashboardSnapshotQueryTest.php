<?php

use App\Application\Dashboard\Queries\GetDashboardSnapshot;
use App\Models\Incident;
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
    expect($snapshot->recentIncidents->count())->toBeLessThanOrEqual(5);

    $expectedLatest = Incident::query()
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->first();

    expect($snapshot->recentIncidents->first()?->id)->toBe($expectedLatest?->id);
});

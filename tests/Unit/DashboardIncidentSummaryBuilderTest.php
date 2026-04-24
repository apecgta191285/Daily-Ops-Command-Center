<?php

declare(strict_types=1);

use App\Application\Dashboard\Support\DashboardIncidentSummaryBuilder;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('dashboard incident summary builder returns stable status pressure and intake counts', function () {
    Carbon::setTestNow(Carbon::parse('2026-04-24 10:00:00'));

    $admin = User::factory()->admin()->create();
    $supervisor = User::factory()->supervisor()->create();

    Incident::factory()->create([
        'title' => 'Owned by admin',
        'status' => IncidentStatus::InProgress->value,
        'severity' => IncidentSeverity::High->value,
        'owner_id' => $admin->id,
        'created_by' => $supervisor->id,
        'follow_up_due_at' => now()->addDay(),
        'created_at' => now()->startOfDay()->addHour(),
    ]);

    Incident::factory()->create([
        'title' => 'Stale and unowned',
        'status' => IncidentStatus::Open->value,
        'severity' => IncidentSeverity::Medium->value,
        'owner_id' => null,
        'created_by' => $admin->id,
        'created_at' => now()->subDays(4),
        'follow_up_due_at' => now()->subDay(),
    ]);

    Incident::factory()->create([
        'title' => 'Resolved yesterday',
        'status' => IncidentStatus::Resolved->value,
        'severity' => IncidentSeverity::Low->value,
        'created_by' => $admin->id,
        'created_at' => now()->subDay()->addHours(2),
        'resolved_at' => now()->subDay()->addHours(5),
    ]);

    $summary = app(DashboardIncidentSummaryBuilder::class)(today(), today()->copy()->subDay(), $admin->id);

    expect($summary['openCount'])->toBeGreaterThanOrEqual(1)
        ->and($summary['inProgressCount'])->toBeGreaterThanOrEqual(1)
        ->and($summary['resolvedCount'])->toBeGreaterThanOrEqual(1)
        ->and($summary['highSeverityUnresolvedCount'])->toBeGreaterThanOrEqual(1)
        ->and($summary['staleUnresolvedCount'])->toBeGreaterThanOrEqual(1)
        ->and($summary['unownedUnresolvedCount'])->toBeGreaterThanOrEqual(1)
        ->and($summary['overdueFollowUpCount'])->toBeGreaterThanOrEqual(1)
        ->and($summary['ownedByActorCount'])->toBeGreaterThanOrEqual(1)
        ->and($summary['todayIncidentIntake'])->toBeGreaterThanOrEqual(1)
        ->and($summary['yesterdayIncidentIntake'])->toBeGreaterThanOrEqual(1);

    Carbon::setTestNow();
});

test('dashboard incident summary builder returns hotspot rows ordered by unresolved count', function () {
    Carbon::setTestNow(Carbon::parse('2026-04-24 10:00:00'));

    $admin = User::factory()->admin()->create();

    Incident::factory()->create([
        'title' => 'Printer issue 1',
        'category' => IncidentCategory::ComputerEquipment->value,
        'status' => IncidentStatus::Open->value,
        'created_by' => $admin->id,
        'created_at' => now()->subDays(3),
    ]);

    Incident::factory()->create([
        'title' => 'Printer issue 2',
        'category' => IncidentCategory::ComputerEquipment->value,
        'status' => IncidentStatus::InProgress->value,
        'created_by' => $admin->id,
        'created_at' => now()->subDays(2),
    ]);

    Incident::factory()->create([
        'title' => 'Network issue 1',
        'category' => IncidentCategory::Network->value,
        'status' => IncidentStatus::Open->value,
        'created_by' => $admin->id,
        'created_at' => now()->subDay(),
    ]);

    Incident::factory()->create([
        'title' => 'Resolved room issue',
        'category' => IncidentCategory::Environment->value,
        'status' => IncidentStatus::Resolved->value,
        'created_by' => $admin->id,
        'created_at' => now()->subDay(),
        'resolved_at' => now(),
    ]);

    $rows = app(DashboardIncidentSummaryBuilder::class)->hotspotRows();

    expect($rows)->toHaveCount(2)
        ->and($rows->first()->category)->toBe(IncidentCategory::ComputerEquipment)
        ->and((int) $rows->first()->unresolved_count)->toBe(2);

    Carbon::setTestNow();
});

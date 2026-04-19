<?php

use App\Application\Incidents\Data\IncidentListFilters;
use App\Application\Incidents\Queries\ListIncidents;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('list incidents query applies stale and unresolved filters consistently', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $fresh = Incident::factory()->create([
        'title' => 'Fresh unresolved',
        'status' => 'Open',
        'created_by' => $admin->id,
    ]);

    $stale = Incident::factory()->create([
        'title' => 'Stale unresolved',
        'status' => 'Open',
        'created_by' => $admin->id,
    ]);

    $stale->forceFill([
        'created_at' => now()->subDays(3),
        'updated_at' => now()->subDays(3),
    ])->saveQuietly();

    $resolved = Incident::factory()->create([
        'title' => 'Resolved old incident',
        'status' => 'Resolved',
        'created_by' => $admin->id,
        'resolved_at' => now(),
    ]);

    $resolved->forceFill([
        'created_at' => now()->subDays(5),
        'updated_at' => now()->subDays(5),
    ])->saveQuietly();

    $results = app(ListIncidents::class)(new IncidentListFilters(
        unresolved: true,
        stale: true,
    ));

    expect($results->pluck('title')->all())->toContain('Stale unresolved');
    expect($results->pluck('title')->all())->not->toContain('Fresh unresolved');
    expect($results->pluck('title')->all())->not->toContain('Resolved old incident');
    expect($results->firstWhere('title', 'Stale unresolved')?->is_stale_for_attention)->toBeTrue();
});

test('list incidents query applies unowned mine and overdue accountability filters consistently', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $supervisor = User::factory()->create(['role' => 'supervisor']);

    $mine = Incident::factory()->create([
        'title' => 'Owned by admin',
        'status' => 'In Progress',
        'created_by' => $admin->id,
        'owner_id' => $admin->id,
        'follow_up_due_at' => today()->addDay(),
    ]);

    $unowned = Incident::factory()->create([
        'title' => 'Unowned incident',
        'status' => 'Open',
        'created_by' => $admin->id,
        'owner_id' => null,
        'follow_up_due_at' => null,
    ]);

    $overdue = Incident::factory()->create([
        'title' => 'Overdue follow-up incident',
        'status' => 'Open',
        'created_by' => $supervisor->id,
        'owner_id' => $supervisor->id,
        'follow_up_due_at' => today()->subDay(),
    ]);

    $resolved = Incident::factory()->create([
        'title' => 'Resolved with old target',
        'status' => 'Resolved',
        'created_by' => $admin->id,
        'owner_id' => $admin->id,
        'follow_up_due_at' => today()->subDays(2),
        'resolved_at' => now(),
    ]);

    $mineResults = app(ListIncidents::class)(new IncidentListFilters(
        mine: true,
        actorId: $admin->id,
    ));

    expect($mineResults->pluck('title')->all())->toContain($mine->title);
    expect($mineResults->pluck('title')->all())->not->toContain($unowned->title);
    expect($mineResults->pluck('title')->all())->not->toContain($overdue->title);

    $unownedResults = app(ListIncidents::class)(new IncidentListFilters(
        unowned: true,
    ));

    expect($unownedResults->pluck('title')->all())->toContain($unowned->title);
    expect($unownedResults->pluck('title')->all())->not->toContain($mine->title);

    $overdueResults = app(ListIncidents::class)(new IncidentListFilters(
        overdue: true,
    ));

    expect($overdueResults->pluck('title')->all())->toContain($overdue->title);
    expect($overdueResults->pluck('title')->all())->not->toContain($mine->title);
    expect($overdueResults->pluck('title')->all())->not->toContain($resolved->title);
    expect($overdueResults->firstWhere('title', $overdue->title)?->is_overdue_follow_up)->toBeTrue();
});

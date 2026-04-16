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

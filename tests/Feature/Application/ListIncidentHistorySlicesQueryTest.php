<?php

declare(strict_types=1);

use App\Application\Incidents\Queries\ListIncidentHistorySlices;
use App\Domain\Access\Enums\UserRole;
use App\Domain\Incidents\Enums\IncidentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('incident history slice query keeps opened and resolved windows bounded to the selected range', function () {
    $admin = $this->createUserForRole(UserRole::Admin);

    $openedInside = $this->createIncidentWithActivity($admin, [
        'title' => 'Opened inside selected range',
        'status' => IncidentStatus::Open->value,
        'created_at' => now()->subDays(2),
    ]);

    $resolvedInside = $this->createIncidentWithActivity($admin, [
        'title' => 'Resolved inside selected range',
        'status' => IncidentStatus::Resolved->value,
        'created_at' => now()->subDays(20),
        'resolved_at' => now()->subDays(1),
    ]);

    $outsideWindow = $this->createIncidentWithActivity($admin, [
        'title' => 'Outside selected range',
        'status' => IncidentStatus::Resolved->value,
        'created_at' => now()->subDays(40),
        'resolved_at' => now()->subDays(35),
    ]);

    $history = app(ListIncidentHistorySlices::class)(7);
    $titles = collect($history['slices'])
        ->flatMap(fn (array $slice): array => [
            ...collect($slice['opened'])->pluck('title')->all(),
            ...collect($slice['resolved'])->pluck('title')->all(),
        ])
        ->values()
        ->all();

    expect($titles)->toContain($openedInside->title)
        ->toContain($resolvedInside->title)
        ->not->toContain($outsideWindow->title)
        ->and($history['opened_count'])->toBeGreaterThanOrEqual(1)
        ->and($history['resolved_count'])->toBeGreaterThanOrEqual(1);
});

test('incident history slice query does not duplicate a same-day opened and resolved incident across buckets', function () {
    $admin = $this->createUserForRole(UserRole::Admin);

    $sameDayIncident = $this->createIncidentWithActivity($admin, [
        'title' => 'Opened and resolved same day',
        'status' => IncidentStatus::Resolved->value,
        'created_at' => now()->subDay(),
        'resolved_at' => now()->subDay()->addHours(2),
    ]);

    $history = app(ListIncidentHistorySlices::class)(7);

    $matchingSlices = collect($history['slices'])
        ->filter(function (array $slice) use ($sameDayIncident): bool {
            $openedIds = collect($slice['opened'])->pluck('id')->all();
            $resolvedIds = collect($slice['resolved'])->pluck('id')->all();

            return in_array($sameDayIncident->id, $openedIds, true)
                || in_array($sameDayIncident->id, $resolvedIds, true);
        })
        ->values();

    expect($matchingSlices)->toHaveCount(1)
        ->and($matchingSlices[0]['opened_count'])->toBeGreaterThanOrEqual(1)
        ->and($matchingSlices[0]['resolved_count'])->toBeGreaterThanOrEqual(1);
});

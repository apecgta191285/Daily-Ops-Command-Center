<?php

declare(strict_types=1);

use App\Application\Dashboard\Support\DashboardRecentHistoryContextBuilder;
use Tests\TestCase;

uses(TestCase::class);

test('dashboard recent history context builder derives unstable and calm states from recent record truth', function () {
    $builder = app(DashboardRecentHistoryContextBuilder::class);

    $unstable = $builder(
        archiveContext: [
            'focus_date' => '2026-04-19',
            'total_runs' => 3,
            'total_not_done_items' => 2,
            'total_noted_items' => 1,
            'lanes' => [
                ['scope' => 'Opening', 'state' => 'covered', 'submitted_count' => 1, 'operator_names' => ['Op A']],
                ['scope' => 'Midday', 'state' => 'warning', 'submitted_count' => 0, 'operator_names' => []],
            ],
        ],
        incidentHistory: [
            'days' => 7,
            'start_date' => '2026-04-13',
            'end_date' => '2026-04-19',
            'opened_count' => 4,
            'resolved_count' => 2,
            'still_active_count' => 1,
            'slices' => [],
        ],
    );

    expect($unstable['state'])->toBe('unstable');
    expect($unstable['headline'])->toBe('Recent operating record still shows carryover');
    expect($unstable['archive']['warning_lanes'])->toBe(1);
    expect($unstable['incidents']['still_active_count'])->toBe(1);

    $calm = $builder(
        archiveContext: [
            'focus_date' => null,
            'total_runs' => 0,
            'total_not_done_items' => 0,
            'total_noted_items' => 0,
            'lanes' => [],
        ],
        incidentHistory: [
            'days' => 7,
            'start_date' => '2026-04-13',
            'end_date' => '2026-04-19',
            'opened_count' => 0,
            'resolved_count' => 0,
            'still_active_count' => 0,
            'slices' => [],
        ],
    );

    expect($calm['state'])->toBe('calm');
    expect($calm['headline'])->toBe('Recent operating record looks settled');
});

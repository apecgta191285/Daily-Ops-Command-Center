<?php

use App\Application\Dashboard\Support\DashboardTrendBuilder;
use Tests\TestCase;

uses(TestCase::class);

test('dashboard trend builder builds rate and count comparisons', function () {
    $builder = app(DashboardTrendBuilder::class);

    expect($builder->buildRateTrend(50, 100))->toBe([
        'todayRate' => 50,
        'yesterdayRate' => 100,
        'difference' => 50,
        'direction' => 'down',
    ]);

    expect($builder->buildCountTrend(4, 4))->toBe([
        'todayCount' => 4,
        'yesterdayCount' => 4,
        'difference' => 0,
        'direction' => 'flat',
    ]);
});

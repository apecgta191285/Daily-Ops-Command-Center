<?php

use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Incidents\Enums\IncidentStatus;
use Carbon\CarbonImmutable;

test('incident stale policy uses a single threshold owner', function () {
    expect(IncidentStalePolicy::thresholdDays())->toBe(2);
});

test('incident stale policy only marks unresolved incidents older than the threshold as stale', function () {
    $now = CarbonImmutable::parse('2026-04-16 12:00:00');

    expect(IncidentStalePolicy::isStale($now->subDays(3), IncidentStatus::Open->value, $now))->toBeTrue();
    expect(IncidentStalePolicy::isStale($now->subDay(), IncidentStatus::Open->value, $now))->toBeFalse();
    expect(IncidentStalePolicy::isStale($now->subDays(10), IncidentStatus::Resolved->value, $now))->toBeFalse();
});

<?php

use App\Application\Incidents\Support\IncidentFollowUpPolicy;
use App\Domain\Incidents\Enums\IncidentStatus;

test('incident follow-up policy marks unresolved incidents overdue only after the target date passes', function () {
    expect(IncidentFollowUpPolicy::isOverdue(
        now()->subDay(),
        IncidentStatus::Open->value,
    ))->toBeTrue();

    expect(IncidentFollowUpPolicy::isOverdue(
        today(),
        IncidentStatus::InProgress->value,
    ))->toBeFalse();

    expect(IncidentFollowUpPolicy::isOverdue(
        now()->subDays(3),
        IncidentStatus::Resolved->value,
    ))->toBeFalse();

    expect(IncidentFollowUpPolicy::isOverdue(
        null,
        IncidentStatus::Open->value,
    ))->toBeFalse();
});

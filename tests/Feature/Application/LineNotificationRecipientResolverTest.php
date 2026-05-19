<?php

use App\Application\Notifications\Support\LineNotificationRecipientResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('line notification recipient resolver deduplicates default and role audiences', function () {
    config([
        'services.line.notifications.to' => 'Cdefault,Csupervisor',
        'services.line.notifications.admin_to' => 'Cadmin',
        'services.line.notifications.supervisor_to' => 'Csupervisor',
        'services.line.notifications.staff_to' => 'Cstaff',
    ]);

    $resolver = new LineNotificationRecipientResolver;

    expect($resolver->forEvent('incident_created'))->toBe([
        'Cdefault',
        'Csupervisor',
        'Cadmin',
    ])->and($resolver->forEvent('incident_status_changed'))->toBe([
        'Cdefault',
        'Csupervisor',
        'Cadmin',
        'Cstaff',
    ])->and($resolver->defaultRecipients())->toBe([
        'Cdefault',
        'Csupervisor',
    ]);
});

test('line notification recipient resolver supports role-only configuration', function () {
    config([
        'services.line.notifications.to' => '',
        'services.line.notifications.admin_to' => 'Cadmin',
        'services.line.notifications.supervisor_to' => 'Csupervisor',
        'services.line.notifications.staff_to' => 'Cstaff',
    ]);

    $resolver = new LineNotificationRecipientResolver;

    expect($resolver->forEvent('incident_created'))->toBe([
        'Cadmin',
        'Csupervisor',
    ])->and($resolver->defaultRecipients())->toBe([]);
});

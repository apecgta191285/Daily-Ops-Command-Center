<?php

use App\Application\Incidents\Support\BackfillLegacyIncidentAttachments;
use App\Application\Notifications\Support\LineNotificationProbe;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('incident-attachments:backfill-private', function (): int {
    $summary = app(BackfillLegacyIncidentAttachments::class)();

    $this->table(
        ['Scanned', 'Migrated', 'Already Private', 'Missing'],
        [[
            $summary['scanned'],
            $summary['migrated'],
            $summary['already_private'],
            $summary['missing'],
        ]]
    );

    $this->info('Legacy incident attachment backfill completed.');

    return 0;
})->purpose('Move legacy incident attachments from the public disk to private local storage.');

Artisan::command('notifications:line:test {--message= : Custom message text for the LINE probe}', function (): int {
    $result = app(LineNotificationProbe::class)($this->option('message'));

    $this->table(
        ['Status', 'HTTP', 'Recipient Type', 'Recipient Fingerprint', 'Message'],
        [[
            $result['status'],
            $result['http_status'] ?? '-',
            $result['recipient_type'] ?? '-',
            $result['recipient_fingerprint'] ?? '-',
            $result['message'],
        ]]
    );

    if ($result['status'] === 'sent') {
        $this->info('LINE notification probe completed successfully.');

        return 0;
    }

    $this->warn('LINE notification probe did not deliver a message. Check configuration, queue/runtime logs, and the notification delivery page.');

    return 1;
})->purpose('Send a LINE Messaging API probe and record the notification delivery result.');

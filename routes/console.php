<?php

use App\Application\Incidents\Support\BackfillLegacyIncidentAttachments;
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

<?php

declare(strict_types=1);

namespace App\Application\Incidents\Support;

use App\Models\Incident;
use Illuminate\Support\Facades\Storage;

class BackfillLegacyIncidentAttachments
{
    /**
     * @return array{scanned:int,migrated:int,missing:int,already_private:int}
     */
    public function __invoke(): array
    {
        $summary = [
            'scanned' => 0,
            'migrated' => 0,
            'missing' => 0,
            'already_private' => 0,
        ];

        Incident::query()
            ->whereNotNull('attachment_path')
            ->orderBy('id')
            ->chunkById(100, function ($incidents) use (&$summary): void {
                foreach ($incidents as $incident) {
                    $summary['scanned']++;

                    $disk = $incident->attachmentDisk();

                    if ($disk === 'local') {
                        $summary['already_private']++;

                        continue;
                    }

                    if ($disk !== 'public') {
                        $summary['missing']++;

                        continue;
                    }

                    $contents = Storage::disk('public')->get($incident->attachment_path);
                    Storage::disk('local')->put($incident->attachment_path, $contents);
                    Storage::disk('public')->delete($incident->attachment_path);

                    $summary['migrated']++;
                }
            });

        return $summary;
    }
}

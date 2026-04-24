<?php

declare(strict_types=1);

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadIncidentAttachmentController extends Controller
{
    public function __invoke(Incident $incident): StreamedResponse
    {
        Gate::authorize('view', $incident);

        $disk = $incident->attachmentDisk();

        abort_if(
            $incident->attachment_path === null || $disk === null,
            404,
            'Attachment not found.'
        );

        return Storage::disk($disk)->response(
            $incident->attachment_path,
            $incident->attachmentDownloadName()
        );
    }
}

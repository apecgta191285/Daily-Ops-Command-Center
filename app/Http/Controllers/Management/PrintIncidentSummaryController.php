<?php

declare(strict_types=1);

namespace App\Http\Controllers\Management;

use App\Application\Incidents\Support\IncidentFollowUpPolicy;
use App\Application\Incidents\Support\IncidentStalePolicy;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Contracts\View\View;

class PrintIncidentSummaryController extends Controller
{
    public function __invoke(Incident $incident): View
    {
        $incident->loadMissing(['creator', 'owner', 'activities.actor']);

        $latestNextActionNote = $incident->activities
            ->where('action_type', 'next_action_note')
            ->sortByDesc('created_at')
            ->first()
            ?->summary;

        $latestResolutionNote = $incident->activities
            ->where('action_type', 'resolution_note')
            ->sortByDesc('created_at')
            ->first()
            ?->summary;

        return view('management.incidents.print-summary', [
            'incident' => $incident,
            'ageInDays' => (int) $incident->created_at->startOfDay()->diffInDays(now()->startOfDay()),
            'isStale' => IncidentStalePolicy::isStale($incident->created_at, $incident->status),
            'staleThresholdDays' => IncidentStalePolicy::thresholdDays(),
            'isFollowUpOverdue' => IncidentFollowUpPolicy::isOverdue($incident->follow_up_due_at, $incident->status),
            'needsOwner' => $incident->status !== IncidentStatus::Resolved && $incident->owner_id === null,
            'latestNextActionNote' => $latestNextActionNote,
            'latestResolutionNote' => $latestResolutionNote,
        ]);
    }
}

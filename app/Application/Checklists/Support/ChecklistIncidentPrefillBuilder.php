<?php

declare(strict_types=1);

namespace App\Application\Checklists\Support;

use App\Application\Checklists\Data\ChecklistIncidentPrefill;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;
use Illuminate\Support\Collection;

class ChecklistIncidentPrefillBuilder
{
    /**
     * @param  array<int, array{result: string|null, note: string|null}>  $runItems
     */
    public function fromDailyRun(ChecklistRun $run, ?ChecklistTemplate $template, array $runItems): ChecklistIncidentPrefill
    {
        /** @var Collection<int, string> $notDoneTitles */
        $notDoneTitles = $run->items
            ->filter(fn ($runItem) => ($runItems[$runItem->id]['result'] ?? null) === ChecklistResult::NotDone->value)
            ->map(fn ($runItem) => $runItem->checklistItem->title)
            ->values();

        $description = collect([
            'Follow-up from the daily checklist.',
            'Template: '.($template?->title ?? '-'),
            'Run date: '.optional($run->run_date)->format('Y-m-d'),
            $run->room?->name ? 'Room: '.$run->room->name : null,
            $notDoneTitles->isNotEmpty() ? 'Items marked Not Done: '.$notDoneTitles->join(', ') : null,
        ])
            ->filter()
            ->implode("\n");

        return new ChecklistIncidentPrefill(
            title: 'Checklist follow-up issue',
            category: IncidentCategory::Other->value,
            severity: $notDoneTitles->isNotEmpty()
                ? IncidentSeverity::Medium->value
                : IncidentSeverity::Low->value,
            description: $description,
            roomId: $run->room_id,
        );
    }
}

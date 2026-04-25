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
            'ติดตามต่อจากรายการตรวจเช็กประจำวัน',
            'แม่แบบ: '.($template?->title ?? '-'),
            'วันที่ตรวจ: '.optional($run->run_date)->format('d/m/Y'),
            $run->room?->name ? 'ห้อง: '.$run->room->name : null,
            $notDoneTitles->isNotEmpty() ? 'รายการที่ไม่เรียบร้อย: '.$notDoneTitles->join(', ') : null,
        ])
            ->filter()
            ->implode("\n");

        return new ChecklistIncidentPrefill(
            title: 'รายงานติดตามจากรายการตรวจเช็ก',
            category: IncidentCategory::Other->value,
            severity: $notDoneTitles->isNotEmpty()
                ? IncidentSeverity::Medium->value
                : IncidentSeverity::Low->value,
            description: $description,
            roomId: $run->room_id,
        );
    }
}

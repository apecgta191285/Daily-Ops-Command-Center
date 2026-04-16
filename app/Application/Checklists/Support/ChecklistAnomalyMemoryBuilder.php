<?php

namespace App\Application\Checklists\Support;

use App\Domain\Checklists\Enums\ChecklistResult;
use App\Models\ChecklistRun;

class ChecklistAnomalyMemoryBuilder
{
    public function __construct(
        private readonly int $recentRunLimit = 3,
    ) {}

    /**
     * @return array<int, array{recent_not_done_count:int, sample_run_count:int, last_not_done_at:?string, last_note:?string}>
     */
    public function forUserAndTemplate(int $userId, int $templateId, ?int $excludeRunId = null): array
    {
        $recentRuns = ChecklistRun::query()
            ->where('created_by', $userId)
            ->where('checklist_template_id', $templateId)
            ->whereNotNull('submitted_at')
            ->when($excludeRunId !== null, fn ($query) => $query->whereKeyNot($excludeRunId))
            ->with('items')
            ->latest('run_date')
            ->limit($this->recentRunLimit)
            ->get();

        $sampleRunCount = $recentRuns->count();

        if ($sampleRunCount === 0) {
            return [];
        }

        $memory = [];

        foreach ($recentRuns as $run) {
            foreach ($run->items as $item) {
                if ($item->result !== ChecklistResult::NotDone->value) {
                    continue;
                }

                $checklistItemId = $item->checklist_item_id;
                $existing = $memory[$checklistItemId] ?? [
                    'recent_not_done_count' => 0,
                    'sample_run_count' => $sampleRunCount,
                    'last_not_done_at' => null,
                    'last_note' => null,
                ];

                $existing['recent_not_done_count']++;

                if ($existing['last_not_done_at'] === null) {
                    $existing['last_not_done_at'] = $run->run_date?->toDateString();
                    $existing['last_note'] = $item->note;
                }

                $memory[$checklistItemId] = $existing;
            }
        }

        return $memory;
    }
}

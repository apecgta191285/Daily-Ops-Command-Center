<?php

namespace App\Application\Checklists\Actions;

use App\Application\Checklists\Data\DailyRunContext;
use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;

class InitializeDailyRun
{
    public function __invoke(int $userId): DailyRunContext
    {
        $templates = ChecklistTemplate::query()
            ->where('is_active', true)
            ->with('items')
            ->get();

        if ($templates->count() === 0) {
            return new DailyRunContext(errorState: 'zero');
        }

        if ($templates->count() > 1) {
            return new DailyRunContext(errorState: 'multiple');
        }

        $template = $templates->first();
        $today = now()->format('Y-m-d 00:00:00');

        $run = ChecklistRun::query()->firstOrCreate(
            [
                'checklist_template_id' => $template->id,
                'run_date' => $today,
                'created_by' => $userId,
            ],
            [
                'assigned_team_or_scope' => $template->scope,
            ],
        );

        if ($run->wasRecentlyCreated) {
            $run->items()->createMany(
                $template->items->map(fn ($item) => [
                    'checklist_item_id' => $item->id,
                ])->all(),
            );
        }

        $run->load('items.checklistItem');

        $recentRuns = ChecklistRun::query()
            ->where('created_by', $userId)
            ->where('submitted_at', '!=', null)
            ->whereKeyNot($run->id)
            ->with('items')
            ->latest('run_date')
            ->limit(3)
            ->get()
            ->map(fn (ChecklistRun $recentRun) => [
                'run_date' => $recentRun->run_date->toDateString(),
                'submitted_at' => $recentRun->submitted_at?->toIso8601String() ?? '',
                'not_done_count' => $recentRun->items->where('result', 'Not Done')->count(),
                'noted_items_count' => $recentRun->items->filter(fn ($item) => filled($item->note))->count(),
            ])
            ->values()
            ->all();

        return new DailyRunContext(
            errorState: null,
            run: $run,
            template: $template,
            runItems: $run->items
                ->mapWithKeys(fn ($runItem) => [
                    $runItem->id => [
                        'result' => $runItem->result,
                        'note' => $runItem->note,
                    ],
                ])
                ->all(),
            recentRuns: $recentRuns,
            isSubmitted: $run->submitted_at !== null,
        );
    }
}

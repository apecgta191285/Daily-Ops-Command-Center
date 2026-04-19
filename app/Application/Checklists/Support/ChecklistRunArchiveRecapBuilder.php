<?php

declare(strict_types=1);

namespace App\Application\Checklists\Support;

use App\Domain\Checklists\Enums\ChecklistResult;
use App\Models\ChecklistRun;

class ChecklistRunArchiveRecapBuilder
{
    /**
     * @return array{
     *   total_items:int,
     *   done_items:int,
     *   not_done_items:int,
     *   noted_items:int,
     *   grouped_items:array<int, array{group:string, items:array<int, array{title:string,result:?string,note:?string,is_required:bool}>}>
     * }
     */
    public function __invoke(ChecklistRun $run): array
    {
        $items = $run->items->map(function ($runItem) {
            return [
                'group' => $runItem->checklistItem?->group_label ?: 'Checklist items',
                'title' => $runItem->checklistItem?->title ?? 'Unknown item',
                'result' => $runItem->result,
                'note' => $runItem->note,
                'is_required' => (bool) ($runItem->checklistItem?->is_required ?? true),
            ];
        });

        return [
            'total_items' => $items->count(),
            'done_items' => $items->where('result', ChecklistResult::Done->value)->count(),
            'not_done_items' => $items->where('result', ChecklistResult::NotDone->value)->count(),
            'noted_items' => $items->filter(fn (array $item): bool => filled($item['note']))->count(),
            'grouped_items' => $items
                ->groupBy('group')
                ->map(fn ($groupItems, $group) => [
                    'group' => (string) $group,
                    'items' => $groupItems->map(fn (array $item) => [
                        'title' => $item['title'],
                        'result' => $item['result'],
                        'note' => $item['note'],
                        'is_required' => $item['is_required'],
                    ])->values()->all(),
                ])
                ->values()
                ->all(),
        ];
    }
}

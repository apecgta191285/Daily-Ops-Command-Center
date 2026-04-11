<?php

declare(strict_types=1);

namespace App\Application\ChecklistTemplates\Actions;

use App\Models\ChecklistTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveChecklistTemplate
{
    public function __invoke(?ChecklistTemplate $template, array $attributes): ChecklistTemplate
    {
        return DB::transaction(function () use ($template, $attributes): ChecklistTemplate {
            $template ??= new ChecklistTemplate;

            if ((bool) $attributes['is_active']) {
                ChecklistTemplate::query()
                    ->when(
                        $template->exists,
                        fn ($query) => $query->whereKeyNot($template->getKey()),
                    )
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $template->fill([
                'title' => $attributes['title'],
                'description' => $attributes['description'] ?: null,
                'scope' => $attributes['scope'],
                'is_active' => (bool) $attributes['is_active'],
            ]);

            $template->save();

            $normalizedItems = collect($attributes['items'])
                ->sortBy(fn (array $item): int => (int) ($item['sort_order'] ?? PHP_INT_MAX))
                ->values();

            $incomingExistingItemIds = $normalizedItems
                ->pluck('id')
                ->filter()
                ->map(fn ($id): int => (int) $id)
                ->all();

            $candidateItemIdsForRemoval = $template->items()
                ->when(
                    filled($incomingExistingItemIds),
                    fn ($query) => $query->whereNotIn('id', $incomingExistingItemIds)->pluck('id'),
                    fn ($query) => $query->pluck('id'),
                )
                ->all();

            $historicalItemIds = empty($candidateItemIdsForRemoval)
                ? []
                : DB::table('checklist_run_items')
                    ->whereIn('checklist_item_id', $candidateItemIdsForRemoval)
                    ->pluck('checklist_item_id')
                    ->all();

            if (! empty($historicalItemIds)) {
                throw ValidationException::withMessages([
                    'items' => 'Checklist items that already have run history cannot be removed. Retire this template and create a new one instead.',
                ]);
            }

            $template->items()
                ->when(
                    filled($candidateItemIdsForRemoval),
                    fn ($query) => $query->whereIn('id', $candidateItemIdsForRemoval),
                    fn ($query) => $query,
                )
                ->delete();

            $keptItemIds = [];

            $normalizedItems->each(function (array $item, int $index) use ($template, &$keptItemIds): void {
                $payload = [
                    'title' => $item['title'],
                    'description' => $item['description'] ?: null,
                    'sort_order' => $index + 1,
                    'is_required' => (bool) $item['is_required'],
                ];

                $existingItem = filled($item['id'] ?? null)
                    ? $template->items()->whereKey($item['id'])->first()
                    : null;

                if ($existingItem) {
                    $existingItem->update($payload);
                    $keptItemIds[] = $existingItem->getKey();

                    return;
                }

                $createdItem = $template->items()->create($payload);
                $keptItemIds[] = $createdItem->getKey();
            });

            return $template->fresh('items');
        });
    }
}

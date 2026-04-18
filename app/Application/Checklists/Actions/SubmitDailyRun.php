<?php

declare(strict_types=1);

namespace App\Application\Checklists\Actions;

use App\Domain\Checklists\Enums\ChecklistResult;
use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitDailyRun
{
    /**
     * @param  array<int, array{result: string|null, note: string|null}>  $runItems
     */
    public function __invoke(ChecklistRun $run, array $runItems, int $actorId): ChecklistRun
    {
        if ($run->submitted_at !== null) {
            return $run->load('items.checklistItem');
        }

        $persistedItems = ChecklistRunItem::query()
            ->where('checklist_run_id', $run->id)
            ->get()
            ->keyBy('id');

        if ($persistedItems->count() !== count($runItems)) {
            throw ValidationException::withMessages([
                'runItems' => ['Checklist submission payload does not match the persisted run items.'],
            ]);
        }

        $invalidIds = collect(array_keys($runItems))
            ->reject(fn ($id) => $persistedItems->has((int) $id));

        if ($invalidIds->isNotEmpty()) {
            throw ValidationException::withMessages([
                'runItems' => ['Checklist submission payload contains unknown run items.'],
            ]);
        }

        $allowedResults = ChecklistResult::values();

        DB::transaction(function () use ($persistedItems, $runItems, $allowedResults, $run, $actorId): void {
            foreach ($runItems as $id => $data) {
                if (! in_array($data['result'], $allowedResults, true)) {
                    throw ValidationException::withMessages([
                        "runItems.{$id}.result" => ['Please answer Done or Not Done.'],
                    ]);
                }

                $persistedItems[(int) $id]->update([
                    'result' => $data['result'],
                    'note' => $data['note'],
                    'checked_by' => $actorId,
                    'checked_at' => now(),
                ]);
            }

            $run->update([
                'submitted_at' => now(),
                'submitted_by' => $actorId,
            ]);
        });

        return $run->fresh(['items.checklistItem']);
    }
}

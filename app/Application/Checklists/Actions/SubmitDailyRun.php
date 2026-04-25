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
                'runItems' => ['ข้อมูลที่ส่งมาไม่ตรงกับรายการตรวจเช็กที่บันทึกไว้ในระบบ'],
            ]);
        }

        $invalidIds = collect(array_keys($runItems))
            ->reject(fn ($id) => $persistedItems->has((int) $id));

        if ($invalidIds->isNotEmpty()) {
            throw ValidationException::withMessages([
                'runItems' => ['ข้อมูลที่ส่งมามีรายการตรวจเช็กที่ระบบไม่รู้จัก'],
            ]);
        }

        $allowedResults = ChecklistResult::values();

        DB::transaction(function () use ($persistedItems, $runItems, $allowedResults, $run, $actorId): void {
            foreach ($runItems as $id => $data) {
                if (! in_array($data['result'], $allowedResults, true)) {
                    throw ValidationException::withMessages([
                        "runItems.{$id}.result" => ['กรุณาเลือกผลตรวจว่าเรียบร้อยหรือไม่เรียบร้อย'],
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

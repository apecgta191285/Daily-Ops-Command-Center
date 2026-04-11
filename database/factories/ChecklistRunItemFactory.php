<?php

namespace Database\Factories;

use App\Models\ChecklistItem;
use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChecklistRunItem>
 */
class ChecklistRunItemFactory extends Factory
{
    protected $model = ChecklistRunItem::class;

    public function definition(): array
    {
        return [
            'checklist_run_id' => ChecklistRun::factory(),
            'checklist_item_id' => ChecklistItem::factory(),
            'result' => null,
            'note' => null,
            'checked_by' => null,
            'checked_at' => null,
        ];
    }
}

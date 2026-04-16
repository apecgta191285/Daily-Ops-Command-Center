<?php

namespace Database\Factories;

use App\Models\ChecklistItem;
use App\Models\ChecklistTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChecklistItem>
 */
class ChecklistItemFactory extends Factory
{
    protected $model = ChecklistItem::class;

    public function definition(): array
    {
        return [
            'checklist_template_id' => ChecklistTemplate::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'group_label' => null,
            'sort_order' => 1,
            'is_required' => true,
        ];
    }
}

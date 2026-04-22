<?php

namespace Database\Factories;

use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChecklistRun>
 */
class ChecklistRunFactory extends Factory
{
    protected $model = ChecklistRun::class;

    public function definition(): array
    {
        return [
            'checklist_template_id' => ChecklistTemplate::factory(),
            'room_id' => null,
            'run_date' => today(),
            'assigned_team_or_scope' => null,
            'created_by' => User::factory(),
            'submitted_at' => null,
            'submitted_by' => null,
        ];
    }

    public function forRoom(?int $roomId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'room_id' => $roomId ?? Room::factory(),
        ]);
    }

    public function submitted(?int $submitterId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'submitted_at' => now(),
            'submitted_by' => $submitterId,
        ]);
    }
}

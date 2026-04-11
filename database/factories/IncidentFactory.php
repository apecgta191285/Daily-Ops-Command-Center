<?php

namespace Database\Factories;

use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(4),
            'category' => fake()->randomElement(IncidentCategory::values()),
            'severity' => fake()->randomElement(IncidentSeverity::values()),
            'status' => IncidentStatus::Open->value,
            'description' => fake()->sentence(),
            'attachment_path' => null,
            'created_by' => User::factory(),
            'resolved_at' => null,
        ];
    }
}

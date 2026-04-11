<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\IncidentActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentActivity>
 */
class IncidentActivityFactory extends Factory
{
    protected $model = IncidentActivity::class;

    public function definition(): array
    {
        return [
            'incident_id' => Incident::factory(),
            'action_type' => 'created',
            'summary' => fake()->sentence(3),
            'actor_id' => User::factory(),
            'created_at' => now(),
        ];
    }
}

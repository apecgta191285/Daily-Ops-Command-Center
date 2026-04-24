<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        $number = fake()->unique()->numberBetween(100, 999);

        return [
            'name' => "Lab {$number}",
            'code' => sprintf('LAB-%03d', $number),
            'description' => "University computer lab room {$number}",
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

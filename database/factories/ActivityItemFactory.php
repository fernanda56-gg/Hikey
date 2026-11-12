<?php

namespace Database\Factories;

use App\Models\ActivityItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityItem>
 */
class ActivityItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->paragraph(),
            'is_completed' => false,
            'order' => $this->faker->numberBetween(0, 2)
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'priority' => 'Baja',
            'status' => 'Disponible',
            'link' => $this->faker->url(),
            'due_date' => null,
            'start_date' => null,
            'completed_at' => null
        ];
    }
}

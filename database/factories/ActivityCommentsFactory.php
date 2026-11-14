<?php

namespace Database\Factories;

use App\Models\ActivityComments;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityComments>
 */
class ActivityCommentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->paragraph(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'link' => $this->faker->url(),
            'link_2' => $this->faker->url(), //image_path
            'status' => 'Pendiente',
            'start_date' => null,
            'end_date' => null,
            'area_id' => Area::inRandomOrder()->first()->id,
        ];
    }
}

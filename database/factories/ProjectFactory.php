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
            'image_path' => $this->faker->url(),
            'link' => $this->faker->imageUrl(640, 480, 'prueba', true),
            'status' => $this->faker->randomElement(['Pendiente', 'En progreso', 'Completado']),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'area_id' => Area::inRandomOrder()->first()->id,
        ];
    }
}

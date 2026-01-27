<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
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
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->companyEmail(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'phone' => $this->faker->phoneNumber(),
            'web_address' => $this->faker->url(),
            'tax_id' => $this->faker->unique()->bothify('?#??##-##??'),
            'company_code' => $this->faker->unique()->regexify('[A-Z]{5}[0-4]{3}'),
        ];
    }
}

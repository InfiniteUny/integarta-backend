<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Institution>
 */
class InstitutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'brick_institution_id' => $this->faker->randomNumber(6, false),
            'name' => $this->faker->sentence(),
            'bank_code' => $this->faker->word(),
            'logo' => $this->faker->word(),
            'type' => $this->faker->word(),
        ];
    }
}

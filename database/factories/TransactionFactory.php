<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'amount' => $this->faker->randomNumber(6, false),
            'description' => $this->faker->sentence(),
            'direction' => $this->faker->randomElement(['in', 'out']),
            'date' => $this->faker->dateTimeBetween('-12 week', '-1 week'),
            'category_name' => $this->faker->word(),
            'classification_group' => $this->faker->word(),
            'classification_subgroup' => $this->faker->word(),
        ];
    }
}

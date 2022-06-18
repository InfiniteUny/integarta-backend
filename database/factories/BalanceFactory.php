<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Balance>
 */
class BalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'balance' => $this->faker->randomNumber(8, true),
            'type' => 'main',
            'name' => null,
        ];
    }

    /**
     * State the model's type.
     *
     * @return static
     */
    public function type($type, $name)
    {
        return $this->state(function (array $attributes) use ($type, $name) {
            return [
                'type' => $type,
                'name' => $name,
            ];
        });
    }
}

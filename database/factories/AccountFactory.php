<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            /* 'user_id' => User::all()->random()->id, */
            'card_number' => fake()->unique()->numberBetween(1000000000, 4294967295),
            'balance' => fake()->randomNumber(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Account;
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
    public function definition(): array
    {
        $statuses = ['pending', 'confirmed', 'cancelled'];
        return [
            'card_id' => Account::factory(),
            'amount' => fake()->randomNumber(),
            'is_deposit' => fake()->boolean(2),
            'fee' => 500,
            'status' => $statuses[array_rand($statuses)]
        ];
    }
}

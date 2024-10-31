<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Example',
            'phone_number' => '09123456789',
            'password' => 'password',
        ]);

        User::factory(300)->create();

        $accounts = Account::factory(600)->create([
            'user_id' => fn() => User::inRandomOrder()->first()->id,
        ]);
        // TODO: change type of creating transactions.
        Transaction::factory(25000)->create([
            'card_id' => fn() => $accounts->random()->id,
            'user_id' => fn($attributes) => $accounts->firstWhere('id', $attributes['card_id'])->user_id,
        ]);
        Transaction::factory(25000)->create([
            'card_id' => fn() => $accounts->random()->id,
            'user_id' => fn($attributes) => $accounts->firstWhere('id', $attributes['card_id'])->user_id,
        ]);
        Transaction::factory(25000)->create([
            'card_id' => fn() => $accounts->random()->id,
            'user_id' => fn($attributes) => $accounts->firstWhere('id', $attributes['card_id'])->user_id,
        ]);
        Transaction::factory(25000)->create([
            'card_id' => fn() => $accounts->random()->id,
            'user_id' => fn($attributes) => $accounts->firstWhere('id', $attributes['card_id'])->user_id,
        ]);

        $this->call(UserBalanceSeeder::class);
    }
}

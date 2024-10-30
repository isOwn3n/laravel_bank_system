<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /* User::factory(10000)->create(); */

        User::factory()->create([
            'name' => 'Alireza',
            'phone_number' => '09396828004',
            'password' => 'amirreza1',
        ]);

        User::factory(100)->create();

        Account::factory(150)->create([
            'user_id' => fn() => User::inRandomOrder()->first()->id,
        ]);

        Transaction::factory(1000)->create([
            'card_id' => fn() => Account::inRandomOrder()->first()->id,
        ]);
        $this->call(UserBalanceSeeder::class);
    }
}

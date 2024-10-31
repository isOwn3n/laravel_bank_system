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
            'name' => 'alireza',
            'phone_number' => '09123456789',
            'password' => 'password',
        ]);
        Account::factory()->create([
            'user_id' => 1,
            'card_number' => 3937919048
        ]);
        Account::factory()->create([
            'user_id' => 1,
            'card_number' => 4287669535
        ]);
        Account::factory()->create([
            'user_id' => 1,
            'card_number' => 1927823329
        ]);

        User::factory(300)->create();

        $accounts = Account::factory(600)->create([
            'user_id' => fn() => User::inRandomOrder()->first()->id,
        ]);

        $totalRecords = 100000;
        $chunkSize = 10000;
        for ($i = 0; $i < $totalRecords; $i += $chunkSize) {
            Transaction::factory($chunkSize)->create([
                'card_id' => fn() => $accounts->random()->id,
                'user_id' => fn($attributes) => $accounts->firstWhere('id', $attributes['card_id'])->user_id,
            ]);
        }

        $this->call(UserBalanceSeeder::class);
    }
}

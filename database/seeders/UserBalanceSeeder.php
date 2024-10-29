<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Get the total balance of accounts associated with the user
            $totalBalance = Account::where('user_id', $user->id)->sum('balance');

            // Update the user balance (assuming you have a `balance` field in the users table)
            $user->balance = $totalBalance;
            $user->save();
        }
    }
}

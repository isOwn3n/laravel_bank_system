<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
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
            $totalBalance = Account::where('user_id', $user->id)->sum('balance');

            $user->balance = $totalBalance;
            $user->save();
        }
    }
}

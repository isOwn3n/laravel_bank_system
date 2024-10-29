<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function update_balance(int $amount, int $user_id, bool $is_deposit): array
    {
        $balance = 0;
        DB::transaction(function () use ($balance, $user_id, $amount, $is_deposit) {
            $user = $this->model->where('id', $user_id)->lockForUpdate()->first();
            if (!$user)
                return [
                    'message' => 'Invalid User.',
                    'status' => 404,
                    'balance' => -1,
                ];

            if ($user->balance < $amount)
                return [
                    'message' => 'There is no enough money.',
                    'status' => 418,
                    'balance' => $user->balance,
                ];

            if ($is_deposit)
                $user->balance += $amount;
            else
                $user->balance -= $amount;

            $user->save();
            $balance = $user->balance;
        });
        return [
            'message' => 'the balance changed successfuly.',
            'status' => 200,
            'balance' => $balance,
        ];
    }
}

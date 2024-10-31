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

    /*
     * This is a function to update balance of user in database.
     * @param int $amount
     * @param int $user_id
     * @param bool $is_deposit
     * @param int $fee (Default: 0)
     *
     * @return array
     */
    public function update_balance(int $amount, int $user_id, bool $is_deposit, int $fee = 0): array
    {
        $data = ['balance' => 0, 'status' => 200, 'message' => 'the balance changed successfuly.'];
        DB::transaction(function () use ($user_id, $amount, $is_deposit, $fee, &$data) {
            $user = $this->model->where('id', $user_id)->lockForUpdate()->first();
            if (!$user) {
                $data['message'] = 'Invalid User.';
                $data['status'] = 404;
                return;
            }

            if ($user->balance < $amount) {
                $data['message'] = 'User dont have enough money.';
                $data['status'] = 418;
                $data['balance'] = $user->balance;
                return;
            }

            $is_deposit ? $user->balance += $amount : $user->balance -= ($amount + $fee);

            $user->save();
            $data['balance'] = $user->balance;
        });
        return $data;
    }

    /**
     * A function to get user an it accounts.
     * @param int $userId
     *
     * @return User|null
     */
    public function getUserAndAccoutns(int $userId): ?User
    {
        return $this->model->with('accounts')->where('id', $userId)->first();
    }
}

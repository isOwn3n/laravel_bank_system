<?php

namespace App\Repositories;

use App\Interfaces\Repositories\AccountRepositoryInterface;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountRepository implements AccountRepositoryInterface
{
    protected Account $model;

    public function __construct(Account $model)
    {
        $this->model = $model;
    }

    /**
     * a function that returns an array it contains balance and a message.
     * @param int $amount
     * @param int $card_number
     * @param int $user_id
     * @return array
     */
    public function update_balance(int $amount, int $card_number, int $user_id, bool $is_deposit = true): array
    {
        /* $balance = 0; */
        /* $account_id = 0; */
        $data = ['balance' => 0, 'id' => 0, 'status' => 200, 'message' => 'the balance changed successfuly.'];
        DB::transaction(function () use ($card_number, $amount, $is_deposit, &$data) {
            $account = $this->model->where('card_number', $card_number)->lockForUpdate()->first();

            if (!$account) {
                $data['balance'] = $account->balance;
                $data['status'] = 404;
                $data['message'] = 'Invalid card number.';
                return;
            }

            if ($account->balance < $amount) {
                $data['balance'] = $account->balance;
                $data['status'] = 418;
                $data['message'] = 'There is no enough money.';
                return;
            }

            $account_id = $account->id;
            $is_deposit ? $account->balance += $amount : $account->balance -= $amount;
            $account->save();

            $balance = $account->balance;
            $account_id = $account->id;
        });
        return $data;
        /* return [ */
        /* 'message' => 'the balance changed successfuly.', */
        /* 'id' => $account_id, */
        /* 'status' => 200, */
        /* 'balance' => $balance, */
        /* ]; */
    }
}

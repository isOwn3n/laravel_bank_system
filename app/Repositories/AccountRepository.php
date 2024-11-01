<?php

namespace App\Repositories;

use App\Interfaces\Repositories\AccountRepositoryInterface;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(protected Account $model) {}

    /**
     * a function that returns an array it contains balance and a message.
     * @param int $amount
     * @param int $card_number
     * @param bool $is_deposit
     * @param int $fee
     * @return array
     */
    public function update_balance(int $amount, int $card_number, bool $is_deposit = true, int $fee = 0): array
    {
        $data = ['balance' => 0, 'id' => 0, 'status' => 200, 'message' => 'the balance changed successfuly.', 'fee' => $fee];
        DB::transaction(function () use ($card_number, $amount, $is_deposit, $fee, &$data) {
            $account = $this->model->where('card_number', $card_number)->lockForUpdate()->first();

            $final_amount = $amount;
            if (!$is_deposit)
                $final_amount += $fee;

            if (!$this->hasBalance($account->id, $final_amount) && !$is_deposit) {
                $data['balance'] = $account->balance;
                $data['status'] = 418;
                $data['message'] = 'There is no enough money.';
                return;
            }

            /* TODO: Write an update function. */
            $is_deposit ? $account->balance += $amount : $account->balance -= $final_amount;
            $account->save();

            $data['balance'] = $account->balance;
            $data['id'] = $account->id;
        });
        return $data;
    }

    public function hasBalance(int $cardId, int $amount): bool
    {
        $account = $this->model->where('id', $cardId)->first();
        $hasBalance = $account->balance - $amount;
        if ($hasBalance < 0)
            return false;
        return true;
    }

    /**
     * A function to get account id by card number.
     * @param int $cardNumber
     * @return int
     */
    public function getAccountId(int $cardNumber): int
    {
        return $this->model->where('card_number', $cardNumber)->first()->id;
    }

    /**
     * A function to get user id by card number.
     * @param int $cardNumber
     * @return int
     */
    public function getUserId(int $cardNumber): int
    {
        return $this->model->where('card_number', $cardNumber)->first()->user_id;
    }
}

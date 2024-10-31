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
     * @param bool $is_deposit
     * @param int $fee
     * @return array
     */
    public function update_balance(int $amount, int $card_number, bool $is_deposit = true, int $fee = 0): array
    {
        $data = ['balance' => 0, 'id' => 0, 'status' => 200, 'message' => 'the balance changed successfuly.', 'fee' => $fee];
        DB::transaction(function () use ($card_number, $amount, $is_deposit, $fee, &$data) {
            $account = $this->model->where('card_number', $card_number)->lockForUpdate()->first();

            if (!$account) {
                $data['status'] = 404;
                $data['message'] = 'Invalid card number.';
                return;
            }

            $final_amount = $amount;
            if (!$is_deposit)
                $final_amount += $fee;

            if ($account->balance < $final_amount && !$is_deposit) {
                $data['balance'] = $account->balance;
                $data['status'] = 418;
                $data['message'] = 'There is no enough money.';
                return;
            }

            $account_id = $account->id;
            $is_deposit ? $account->balance += $amount : $account->balance -= $final_amount;
            $account->save();

            $data['balance'] = $account->balance;
            $data['id'] = $account_id;
        });
        return $data;
    }

    /**
     * A function to get total transaction of day.
     * @param int $card_number
     * @return int
     */
    public function today_total_amount(int $card_number): int
    {
        $accout = $this->model->where('card_number', $card_number)->first();
        if (!$accout)
            return -1;

        $total_amount = $accout
            ->transactions()
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        return (int) $total_amount;
    }

    /**
     * This is a function check card total transactions in a day.
     * @param int $card_number
     * @param int $amount
     * @return bool
     */
    public function is_account_able(int $card_number, int $amount): bool
    {
        $total_amount = $this->today_total_amount($card_number);

        if ($total_amount + $amount > 50000000 || $total_amount == -1)
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

    public function getUserId(int $cardNumber): int
    {
        return $this->model->where('card_number', $cardNumber)->first()->user_id;
    }
}

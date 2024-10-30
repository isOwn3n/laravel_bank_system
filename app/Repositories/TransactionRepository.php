<?php

namespace App\Repositories;

use App\Interfaces\Repositories\TransactionRepositoryInterface;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    protected Transaction $model;
    protected UserRepository $userRepository;
    protected AccountRepository $accountRepository;

    public function __construct(
        Transaction $model,
        UserRepository $userRepository,
        AccountRepository $accountRepository,
    ) {
        $this->model = $model;
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * A Select Query to get count of successful trasactions per hour
     * @param int $user_id
     * @param int $card_number
     * @return int
     */
    public function successfulTrasactionsPerHourCount(int $user_id, int $card_number): int
    {
        $oneHourAgo = Carbon::now()->subHour();
        $transactions = $this->model->where('updated_at', '>=', $oneHourAgo)->where('status', 'confirmed')->get();
        return $transactions->count();
    }

    /**
     * This is a function to get Cash and
     * @param int $user_id
     * @param int $card_number
     * @param int $amount
     * @return array
     */
    public function getCash(int $balance, int $account_id, int $amount, int $fee): array
    {
        DB::transaction(function () use ($amount, $account_id, $fee) {
            $transaction = $this->model->create([
                'card_id' => $account_id,
                'is_deposit' => false,
                'amount' => $amount,
                'fee' => $fee,
                'status' => 'confirmed'
            ]);
        });

        return [
            'message' => 'You got cash successfully.',
            'balance' => $balance,
        ];
    }

    // TODO: Change name of this function.
    public function get_most_payed_users(int $user_id)
    {
        return;
    }

    /* public function */
}

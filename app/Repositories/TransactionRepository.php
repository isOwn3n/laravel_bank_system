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
    public function getCash(int $user_id, int $card_number, int $amount): array
    {
        // TODO: Move logics to Service files. (here just get data and update it.)
        $user = $this->userRepository->update_balance($amount, $user_id, false);
        if ($user['status'] != 200)
            return $user;

        $account = $this->accountRepository->update_balance($amount, $card_number, $user_id, false);
        if ($account['status'] != 200)
            return $account;

        $account_id = $account['id'];

        DB::transaction(function () use ($amount, $account_id) {
            $transaction = $this->model->create([
                'card_id' => $account_id,
                'is_deposit' => false,
                'amount' => $amount,
            ]);
        });

        return [
            'message' => 'You got cash successfully.',
            'balance' => $account['balance'],
        ];
    }
}

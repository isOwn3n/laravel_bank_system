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
     * @param int $account_id
     * @param int $user_id
     * @param int $fee
     * @param bool $is_deposit
     * @param string $status='pending'
     */
    public function create(int $account_id, int $user_id, int $amount, int $fee = 0, bool $is_deposit = true,
        string $status = 'pending', ?int $destCardId = NULL)
    {
        DB::beginTransaction();

        try {
            $transaction = $this->model->create([
                'card_id' => $account_id,
                'dest_card_id' => $destCardId,
                'user_id' => $user_id,
                'is_deposit' => $is_deposit,
                'amount' => $amount,
                'fee' => $fee,
                'status' => $status
            ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->model->create([
                'card_id' => $account_id,
                'dest_card_id' => $destCardId,
                'user_id' => $user_id,
                'is_deposit' => $is_deposit,
                'amount' => $amount,
                'fee' => $fee,
                'status' => 'pending'
            ]);

            return false;
        }
    }

    /**
     * This is a function to get Cash.
     * @param int $balance
     * @param int $account_id
     * @param int $user_id
     * @param int $amount
     * @param int $fee
     * @return array
     */
    public function getCash(int $balance, int $account_id, int $user_id, int $amount, int $fee): array
    {
        DB::transaction(function () use ($amount, $account_id, $fee, $user_id) {
            $this->create($account_id, $user_id, $amount, $fee, false, 'confirmed');
        });

        return [
            'message' => 'You got cash successfully.',
            'balance' => $balance,
        ];
    }

    // TOOD: Change name of this function.

    /**
     * This is a function that returns user info and they 10 last transactions.
     * @param array $ids
     * @return array
     */
    public function get_last_ten_rows(array $ids): array
    {
        $transactions_by_user = [];

        foreach ($ids as $user_id => $amount) {
            $transaction = $this
                ->model
                ->where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            $transactions_by_user[$user_id]['info'] = $transaction->first()->user;
            $transactions_by_user[$user_id]['transactions'] = $transaction;
        }

        return $transactions_by_user;
    }

    public function transfer(int $userId, int $srcCardId, int $destCardId, int $amount, int $fee = 0)
    {
        $withdrawal = $this->create($srcCardId, $userId, $amount, $fee, false, 'confirmed', $destCardId);
        $deposit = $this->create($destCardId, $userId, $amount, 0, true, 'confirmed');

        if (!($withdrawal && $deposit))
            return false;
        return true;
    }

    // DATABASE TASK.
    public function countOfTransactionsInLastHour()
    {
        $count = $this
            ->model
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();
        return $count;
    }

    public function amountOfTransactionsInLastMonthPerUser()
    {
        $user_id = 1;
        $totalAmount = $this
            ->model
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->where('user_id', $user_id)
            ->sum('amount');
        return $totalAmount;
    }

    public function amountOfTransactionsInLastMonthPerUsersCard()
    {
        $userId = 1;
        $userTotalAmount = Transaction::where('created_at', '>=', Carbon::now()->subMonth())
            ->where('user_id', $userId)
            ->sum('amount');
        $cardsTotalAmounts = Transaction::where('created_at', '>=', Carbon::now()->subMonth())
            ->where('user_id', $userId)
            ->groupBy('card_id')
            ->select('card_id')
            ->selectRaw('SUM(amount) AS total_amount')
            ->get();
        return ['user' => $userTotalAmount, 'cards' => $cardsTotalAmounts];
    }
}

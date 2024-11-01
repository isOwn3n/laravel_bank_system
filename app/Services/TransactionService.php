<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use Illuminate\Http\JsonResponse;

class TransactionService
{
    public function __construct(
        protected TransactionRepository $repository,
        public UserService $userService,
        public AccountService $accountService,
    ) {}

    /**
     * This is a function to handle getting cash proccess
     * @param int $userId
     * @param int $cardNumber
     * @param int $amount
     * @return JsonResponse
     */
    public function getCash(int $userId, int $cardNumber, int $amount): JsonResponse
    {
        $maxAmountOfTransactions = env('MAX_TRANSACTION', 50000000);

        $fee = env('FEE', 500);

        $account = $this->accountService->update_balance($amount, $cardNumber, false, $fee);
        switch ($account['status']) {
            case 404:
                return response()->json([
                    'message' => $account['message'],
                ], $account['status']);
                break;
            case 403:
                return response()->json([
                    'message' => $account['message'],
                ], $account['status']);
                break;
            case 418:
                return response()->json([
                    'message' => $account['message'],
                    'balance' => $account['balance'],
                ], $account['status']);
                break;

            default:
                break;
        }

        $accountId = $account['id'];
        $result = $this->repository->getCash($account['balance'], $account['id'], $userId, $amount, $fee);
        return response()->json([$result], 200);
    }

    /**
     * This is a function get ten last row of a list of users.
     * @param array $userIds
     * @return array
     */
    public function getTopUsers(array $userIds): array
    {
        return $this->repository->get_last_ten_rows($userIds);
    }

    /**
     * This is a function parse all transactions got from redis.
     * @param array $cacheTransactions
     * @return array
     */
    public function getAllTransactions(array $cacheTransactions): array
    {
        $transactions = [];
        foreach ($cacheTransactions as $transaction) {
            $redisKey = str_replace('laravel_database_', '', $transaction);

            $transactionData = getFromRedisAsJson($redisKey);

            $amount = $transactionData['amount'] ?? 0;
            $userId = explode(':', $redisKey)[1];

            if (!isset($transactions[$userId]))
                $transactions[$userId] = [];

            $transactions[$userId][] = $amount;
        }
        return $transactions;
    }

    /**
     * This is a function to filter top 3 users with most amount of transactions in last 10 minutes got from redis.
     * @param array $transactions
     * @return array
     */
    public function getThreeTopUsers(array $transactions): array
    {
        $totalAmounts = [];
        foreach ($transactions as $userId => $amounts) {
            $totalAmounts[$userId] = array_sum($amounts);
        }
        arsort($totalAmounts);
        $topUsers = array_slice($totalAmounts, 0, 3, true);
        return $topUsers;
    }

    /**
     * A function to access getBalance function from user service.
     * @param int $userId
     * @return array
     */
    public function getBalance(int $userId): array
    {
        return $this->userService->getBalance($userId);
    }

    /*
     * This is a function to handle transfer stuff.
     * @param int $userId
     * @param int $srcCardNumber
     * @param int $destCardNumber
     * @param int $amount
     * @param int $fee The fee of transactions (default: 0)
     * @return bool
     */
    public function transfer(int $userId, int $srcCardNumber, int $destCardNumber, int $amount, int $fee = 0): bool
    {
        $srcCardId = $this->accountService->getAccountId($srcCardNumber);
        $destCardId = $this->accountService->getAccountId($destCardNumber);
        $transferResult = $this->repository->transfer($userId, $srcCardId, $destCardId, $amount, $fee);
        if ($transferResult) {
            $srcAccount = $this->accountService->update_balance($amount, $srcCardNumber, false, $fee);

            $destUserId = $this->accountService->getUserIdByAccount($destCardNumber);
            $destAccount = $this->accountService->update_balance($amount, $destCardNumber);
            return true;
        }
        return false;
    }
}

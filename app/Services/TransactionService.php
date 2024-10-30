<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/*
 * Get The Current Total Amount of Each Card (Account) And set it in redis, at the end of day, remove it. (Using Worker.)
 * - What Happens If The Server Goes Down?
 * - All Data Will Lost.
 */

class TransactionService
{
    public function __construct(
        public TransactionRepository $reposotiry,
        public UserRepository $userRepository,
        public AccountService $accountService,
    ) {}

    public function getTotalTransactionsPerDay(int $user_id)
    {
        $today = Carbon::today();

        $transactions = Transaction::where('user_id', $user_id)->whereDate('updated_at', $today)->get();
        $balance = $transactions->sum('amount');
        return response()->json(['balance' => $balance], 200);
    }

    public function isCardLimited(int $card_number, int $amount): bool
    {
        return true;
    }

    public function getCash(int $user_id, int $card_number, int $amount): JsonResponse
    {
        $today_transactions = $this->accountService->get_total_transactions_of_day($card_number);
        $max_amount_of_transactions = env('MAX_TRANSACTION', 50000000);

        $fee = env('FEE', 500);
        if (
            $today_transactions > $max_amount_of_transactions ||
            $today_transactions + $amount + $fee > $max_amount_of_transactions
        ) {
            $allowed_amount = $max_amount_of_transactions - $today_transactions;
            if ($allowed_amount < 0)
                $allowed_amount = 0;

            return response()->json(['message' => 'Today you cant do any transactions.', 'allowed_amount' => $allowed_amount], 403);
        }

        $amount += $fee;

        // TODO: Dont check user anymore
        $user = $this->userRepository->update_balance($amount, $user_id, false);
        switch ($user['status']) {
            case 404:
                return response()->json([
                    'message' => $user['message'],
                ], $user['status']);
                break;
            case 418:
                return response()->json([
                    'message' => $user['message'],
                    'balance' => $user['balance'],
                ], $user['status']);
                break;

            default:
                break;
        }

        $account = $this->accountService->update_balance($amount, $card_number, $user_id, false);
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

        $account_id = $account['id'];
        $result = $this->reposotiry->getCash($account['balance'], $account['id'], $amount, $fee);
        return response()->json([$result], 200);
    }
}

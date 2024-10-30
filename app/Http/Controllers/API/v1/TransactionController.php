<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected TransactionService $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function testRedis()
    {
        return response()->json($this->read_all_from_redis());
    }

    /**
     * A controller to get cash (API).
     * @param CashRequest $request A Custom Request Class.
     * @return JsonResponse
     */
    public function cash(CashRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user_id = $request->user()->id;
        $result = $this->service->getCash($user_id, $validated['card_number'], $validated['amount']);

        $this->write_in_redis($validated['card_number'], $user_id, $validated['amount']);

        return $result;
    }

    /**
     * @return JsonResponse
     */
    public function get_three_last_users(): JsonResponse
    {
        $cache_transaction = $this->read_all_from_redis();
        $transactions = [];
        foreach ($cache_transaction as $transaction) {
            // TODO: Clean this code.
            $redisKey = str_replace('laravel_database_', '', $transaction);
            $transaction_data = $this->get_from_redis($redisKey);
            $amount = json_decode($transaction_data, true)['amount'] ?? 0;
            $user_id = explode(':', $redisKey)[1];

            if (!isset($transactions[$user_id])) {
                $transactions[$user_id] = [];
            }
            $transactions[$user_id][] = $amount;
        }
        $totalAmounts = [];
        foreach ($transactions as $userId => $amounts) {
            $totalAmounts[$userId] = array_sum($amounts);
        }
        arsort($totalAmounts);
        $top3Users = array_slice($totalAmounts, 0, 3, true);

        $transactions_result = $this->service->reposotiry->get_last_ten_rows($top3Users);
        return response()->json($transactions_result);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */

    // TODO: Remove it and write a query (One with SQL and the other one with ORM)
    public function get_count_per_hour(Request $request): JsonResponse
    {
        $transactions_count = $this->repository->successfulTrasactionsPerHourCount(1, 3198572955);
        return response()->json(
            ['count' => $transactions_count, 'message' => 'Count of successful transactions at last hour'],
        );
    }
}

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
        return response()->json($this->read_from_redis_by_user_id(1));
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

    public function get_three_last_users(Request $request)
    {
        $cache_transaction = $this->read_all_from_redis();
        $user_ids = [];
        foreach ($cache_transaction as $index => $value) {
            $parts = explode(':', $value);
            array_push($user_ids, (int) $parts[1]);
        }
        return response()->json($user_ids);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
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

<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashRequest;
use App\Http\Requests\TransferRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    protected TransactionService $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
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
        $card_number = $validated['card_number'];
        $amount = $validated['amount'];
        $result = $this->service->getCash($user_id, $card_number, $amount);

        $this->writeInRedis($card_number, $user_id, $amount);

        return $result;
    }

    /**
     * A controller to get 3 users they had most amount of transactions in last 10 minutes.
     * @return JsonResponse
     */
    public function getThreeLastUsers(): JsonResponse
    {
        $cacheTransactions = $this->readAllTransactionsFromRedis();
        $transactions = $this->service->getAllTransactions($cacheTransactions);

        $topUsers = $this->service->getThreeTopUsers($transactions);

        $tenLastTransactionsOfUsers = $this->service->getTopUsers($topUsers);

        return response()->json($tenLastTransactionsOfUsers);
    }

    /**
     * A controller to get balance for user and it cards.
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalance(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $balances = $this->service->getBalance($userId);
        return response()->json($balances);
    }

    public function transfer(TransferRequest $request): JsonResponse
    {
        // TODO: Clean this part of code.
        $validated = $request->validated();

        if ($validated['card_number'] === $validated['dest_card_number'])
            return response()->json([
                'message' => 'The destination card number must be different from the source card number.',
            ], Response::HTTP_FORBIDDEN);

        $userId = $request->user()->id;
        $fee = (int) env('FEE', 500);
        $srcCardNumber = $validated['card_number'];
        $amount = $validated['amount'];
        $destCardNumber = $validated['dest_card_number'];

        $this->service->transfer(
            $userId,
            $srcCardNumber,
            $destCardNumber,
            $amount,
            $fee,
        );
        $this->writeInRedis($srcCardNumber, $userId, $amount);

        return response()->json([
            'message' => 'Transfer was successful.',
            'amount' => $amount,
            'fee' => $fee,
            'dest_card_number' => $destCardNumber
        ], Response::HTTP_OK);
    }

    /**
     * A Temp controller to get count of transactions in last hour.
     * @param Request $request
     * @return JsonResponse
     */

    // TODO: Remove it and write a query (One with SQL and the other one with ORM)
    public function get_count_per_hour(Request $request): JsonResponse
    {
        $transactions_count = $this->repository->successfulTrasactionsPerHourCount(1, 3198572955);
        return response()->json(
            [
                'count' => $transactions_count,
                'message' => 'Count of successful transactions at last hour',
            ], Response::HTTP_OK
        );
    }
}

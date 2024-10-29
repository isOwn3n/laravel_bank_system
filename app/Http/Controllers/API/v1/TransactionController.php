<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\Repositories\TransactionRepositoryInterface;
use App\Models\Account;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    protected TransactionService $service;
    protected TransactionRepositoryInterface $repository;

    public function __construct(TransactionService $service, TransactionRepositoryInterface $repository)
    {
        $this->service = $service;
        /* Move the repository to service. */
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->service->getTotalTransactionsPerDay(1);
    }

    /* TODO: Write The Custom Request. */
    public function cash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|integer|exists:accounts,card_number',
            'amount' => 'required|integer'
        ]);
        if ($validator->fails())
            return response()->json([
                'message' => 'Error: ' . $validator->errors()
            ], 422);

        $validated_data = $validator->validated();
        return response()->json($this->repository->getCash(1, $validated_data['card_number'], $validated_data['amount']));
        /* $balance = Account::where('card_number', $validated_data['card_number'])->where('user_id', $request->user()->id); */
        /* if ($balance < $validated_data['amount']) */
        /* return response()->json(['message' => 'Not Enough Money!'], Response::HTTP_I_AM_A_TEAPOT); */
    }

    /**
     * Store a newly created resource in storage.
     */
    public function get_count_per_hour(Request $request)
    {
        $transactions_count = $this->repository->successfulTrasactionsPerHourCount(1, 3198572955);
        return response()->json(
            ['count' => $transactions_count, 'message' => 'Count of successful transactions at last hour'],
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

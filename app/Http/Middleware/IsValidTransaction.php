<?php

namespace App\Http\Middleware;

use App\Rules\CardNumberBelongsToUser;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class IsValidTransaction
{
    public function __construct(
        public TransactionService $transactionService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'card_number' => ['required', 'integer', 'exists:accounts,card_number', new CardNumberBelongsToUser()],
            'amount' => ['required', 'integer', 'min:1000', 'max:50000000'],
        ];

        $validated = $request->validate($rules, $request->all());

        $cardNumber = $validated['card_number'];
        $amount = $validated['amount'];

        $cardTotalAmount = getTotalTransactionsOfUsersCard($request->user()->id, $cardNumber) ?? 0;

        $maxTransactionAmount = (int) env('MAX_TRANSACTION', 50000000);

        if ($cardTotalAmount > $maxTransactionAmount ||
                $cardTotalAmount + $amount > $maxTransactionAmount)
            return response()->json([
                'message' => 'Your transaction limit has been reached.',
                'transaction_limit' => $maxTransactionAmount,
            ], Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}

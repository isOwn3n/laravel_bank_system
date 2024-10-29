<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/*
 * Get The Current Total Amount of Each Card (Account) And set it in redis, at the end of day, remove it. (Using Worker.)
 * - What Happens If The Server Goes Down?
 * - All Data Will Lost.
 */

class TransactionService
{

    /* Method #1 (DB) */
    public function getTotalTransactionsPerDay(int $user_id)
    {
        $today = Carbon::today();

        /*echo $mytime->toDateTimeString();*/
        $transactions = Transaction::where('user_id', $user_id)->whereDate('updated_at', $today)->get();
        $balance = $transactions->sum('amount');
        return response()->json(['balance' => $balance], 200);
    }
}

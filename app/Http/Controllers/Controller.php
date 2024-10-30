<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;

abstract class Controller
{
    public function read_from_redis_by_user_id(int $user_id)
    {
        $keys = Redis::keys("*transaction:{$user_id}:*");
        return $keys;
    }

    public function read_all_from_redis()
    {
        $keys = Redis::keys('*transaction:*');
        return $keys;
    }

    // TODO: Add everything else need here.
    public function write_in_redis(int $card_number, int $user_id, int $amount): void
    {
        Redis::setex('test_key', 600, 'test_value');
        Redis::setex("transaction:{$user_id}:{$card_number}", 600, json_encode([
            'amount' => $amount
        ]));
    }
}

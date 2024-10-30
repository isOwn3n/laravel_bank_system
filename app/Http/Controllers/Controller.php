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

    public function write_in_redis(int $card_number, int $user_id, int $amount): void
    {
        $current_time = time();
        Redis::setex("transaction:{$user_id}:{$card_number}:{$current_time}", 600, json_encode([
            'amount' => $amount
        ]));
    }

    public function get_from_redis(string $key)
    {
        return Redis::get($key);
    }
}

<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

if (!function_exists('getFromRedis')) {
    /**
     * A helper function to get value of a specific key in redis.
     * @param string $key
     * @return ?string
     */
    function getFromRedis(string $key): ?string
    {
        return Redis::get($key);
    }
}

if (!function_exists('setExToRedis')) {
    /**
     * A helper function to set a data in redis with an expire time.
     * @param string $key
     * @param string $value
     * @param int $expireAt
     * @return void
     */
    function setExToRedis(string $key, string $value, int $expireAt): void
    {
        Redis::setex($key, $expireAt, $value);
    }
}

if (!function_exists('keysInRedis')) {
    /*
     * You can give the user id in this key for example:
     * "transaction:{$userId}"
     * and it returns user keys
     */

    /**
     * A helper function to get all keys with an specific prefix in redis.
     * @param string $keyPrefix
     * @return array
     */
    function keysInRedis(string $keyPrefix): array
    {
        return Redis::keys($keyPrefix . ':*');
    }
}

if (!function_exists('setTransactionWithMidnightExpiry')) {
    /**
     * A helper function to store a value in redis to validate total amount of transaction of each user on each of their cards.
     * @param int $cardNumber
     * @param int $userId
     * @param int $amount
     * @return string
     */
    function setTransactionWithMidnightExpiry(int $cardNumber, int $userId, int $amount): string
    {
        $now = Carbon::now();
        $midnight = Carbon::tomorrow()->startOfDay();
        $expire_at = (int) $now->diffInSeconds($midnight);

        $key = generateRedisKey($userId, $cardNumber, 'total_transaction', false);

        $last_amount = json_decode(getFromRedis($key), true);
        if ($last_amount)
            $amount += $last_amount['amount'];

        $value = json_encode(['amount' => $amount]);

        setExToRedis($key, $value, $expire_at);
        return $key;
    }
}

if (!function_exists('setEachTransactionWithTenMExpiry')) {
    /**
     * A helper function to store transactions in redis.
     * @param int $userId
     * @param int $cardNumber
     * @param int $amount
     * @return void
     */
    function setEachTransactionWithTenMExpiry(int $userId, int $cardNumber, int $amount): void
    {
        $key = generateRedisKey($userId, $cardNumber);
        $value = json_encode(['amount' => $amount]);
        setExToRedis($key, $value, 600);
    }
}

if (!function_exists('generateRedisKey')) {
    /**
     * A helper function to generate string with an specific format for keys of redis.
     * @param int $userId
     * @param int $cardNumber
     * @param string $prefix default value 'transaction'
     * @param bool $hasTimeStamp default value true
     * return string
     */
    function generateRedisKey(int $userId, int $cardNumber, string $prefix = 'transaction', bool $hasTimeStamp = true): string
    {
        $key = "{$prefix}:{$userId}:{$cardNumber}";
        if ($hasTimeStamp)
            $key = $key . ':' . time();
        return $key;
    }
}

if (!function_exists('isKeyValid')) {
    /**
     * A very simple helper function to validate key structure.
     * @param string $key
     * @return bool
     */
    function isKeyValid(string $key): bool
    {
        $splittedKey = explode(':', $key);
        if (in_array(count($splittedKey), [3, 4]))
            return true;
        return false;
    }
}

if (!function_exists('getTotalTransactionsOfUsersCard')) {
    /**
     * A helper function to get total amount of transactions of users card.
     * @param int $userId
     * @param $cardNumber
     * @return int
     */
    function getTotalTransactionsOfUsersCard(int $userId, int $cardNumber)
    {
        $key = generateRedisKey($userId, $cardNumber, 'total_transaction', false);
        $totalAmount = getFromRedis($key);
        $jsonTotalAmount = json_decode($totalAmount, true)['amount'] ?? 0;
        return $jsonTotalAmount ?? 0;
    }
}

if (!function_exists('getFromRedisAsJson')) {
    function getFromRedisAsJson(string $key): array
    {
        $data = getFromRedis($key);
        $data_json = json_decode($data, true);
        return $data_json;
    }
}

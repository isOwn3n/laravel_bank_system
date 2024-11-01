<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

if (!function_exists('getFromCache')) {
    function getFromCache(string $key)
    {
        return Cache::get($key);
    }
}

if (!function_exists('setToCache')) {
    function setToCache(string $key, string $value, int $expireAtSeconds)
    {
        Cache::put($key, $value, (int) ($expireAtSeconds));
    }
}

if (!function_exists('keysInCache')) {
    /*
     * You can give the user id in this key for example:
     * "transaction:{$userId}"
     * and it returns user keys
     */

    /**
     * A helper function to get all keys with an specific prefix in cache.
     * @param string $keyPrefix
     * @return array
     */
    function keysInCache(string $keyPrefix): array
    {
        // I Have to use redis. Cache dont have any function to this action.
        return Redis::connection('cache')->keys('*');
    }
}

if (!function_exists('setTransactionWithMidnightExpiryCache')) {
    /**
     * A helper function to store a value in redis to validate total amount of transaction of each user on each of their cards.
     * @param int $cardNumber
     * @param int $userId
     * @param int $amount
     * @return string
     */
    function setTransactionWithMidnightExpiryCache(int $cardNumber, int $userId, int $amount): string
    {
        /* Get expire time from now to 12 A.M. */
        $now = Carbon::now();
        $midnight = Carbon::tomorrow()->startOfDay();
        $expire_at = (int) $now->diffInSeconds($midnight);

        /* Generate A Key with an specific format. */
        $key = generateKey($userId, $cardNumber, 'total_transaction', false);

        /* Update total amount of users card. */
        $last_amount = json_decode(getFromCache($key), true);
        if ($last_amount)
            $amount += $last_amount['amount'];

        $value = json_encode(['amount' => $amount]);

        setToCache($key, $value, $expire_at);
        return $key;
    }
}

if (!function_exists('setEachTransactionWithTenMExpiryCache')) {
    /**
     * A helper function to store all transactions in redis.
     * @param int $userId
     * @param int $cardNumber
     * @param int $amount
     * @return void
     */
    function setEachTransactionWithTenMExpiryCache(int $userId, int $cardNumber, int $amount): void
    {
        $key = generateKey($userId, $cardNumber);
        $value = json_encode(['amount' => $amount]);
        setToCache($key, $value, 600);
    }
}

if (!function_exists('generateKey')) {
    /**
     * A helper function to generate string with an specific format and structure for keys of redis.
     * @param int $userId
     * @param int $cardNumber
     * @param string $prefix default value 'transaction'
     * @param bool $hasTimeStamp default value true
     * return string
     */
    function generateKey(int $userId, int $cardNumber, string $prefix = 'transaction', bool $hasTimeStamp = true): string
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

        /* Len of keys should be 3 or 4. (It depends on timestamp set or not.) */
        return in_array(count($splittedKey), [3, 4]);
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
        /* Generate key to get total transactions, today. */
        $key = generateKey($userId, $cardNumber, 'total_transaction', false);
        $totalAmount = getFromCacheAsJson($key)['amount'] ?? 0;
        return $totalAmount;
    }
}

if (!function_exists('getFromCacheAsJson')) {
    function getFromCacheAsJson(string $key): ?array
    {
        $data = getFromCache($key);
        $data_json = json_decode($data, true);
        return $data_json;
    }
}

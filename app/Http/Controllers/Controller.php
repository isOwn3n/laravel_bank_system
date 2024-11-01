<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function readFromCacheByUserId(int $userId): array
    {
        return keysInCache("transaction:{$userId}");
    }

    public function readAllTransactionsFromCache(): array
    {
        return keysInCache('transaction');
    }

    public function writeInCache(int $cardNumber, int $userId, int $amount): void
    {
        setEachTransactionWithTenMExpiryCache($userId, $cardNumber, $amount);
        setTransactionWithMidnightExpiryCache($cardNumber, $userId, $amount);
    }
}

<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function readFromRedisByUserId(int $userId): array
    {
        return keysInRedis("transaction:{$userId}");
    }

    public function readAllTransactionsFromRedis(): array
    {
        return keysInRedis('transaction');
    }

    public function writeInRedis(int $cardNumber, int $userId, int $amount): void
    {
        setEachTransactionWithTenMExpiry($userId, $cardNumber, $amount);
        setTransactionWithMidnightExpiry($cardNumber, $userId, $amount);
    }
}

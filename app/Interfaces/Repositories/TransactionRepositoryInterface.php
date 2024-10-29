<?php

namespace App\Interfaces\Repositories;

interface TransactionRepositoryInterface
{
    public function successfulTrasactionsPerHourCount(int $user_id, int $card_number): int;
    public function getCash(int $user_id, int $card_number, int $amount): array;

    /* public function successfulTrasactionsPerHour(int $user_id, int $card_number): int; */
}

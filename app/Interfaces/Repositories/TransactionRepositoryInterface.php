<?php

namespace App\Interfaces\Repositories;

interface TransactionRepositoryInterface
{
    public function successfulTrasactionsPerHourCount(int $user_id, int $card_number): int;

    public function getCash(int $balance, int $account_id, int $user_id, int $amount,
        int $fee): array;

    public function create(int $account_id, int $user_id, int $amount, int $fee = 0, bool $is_deposit = true,
        string $status = 'pending', ?int $destCardId = NULL);

    public function get_last_ten_rows(array $ids): array;

    public function transfer(int $userId, int $srcCardId, int $destCardId, int $amount, int $fee = 0);
}

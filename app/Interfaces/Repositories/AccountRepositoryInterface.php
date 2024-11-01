<?php

namespace App\Interfaces\Repositories;

interface AccountRepositoryInterface
{
    public function update_balance(int $amount, int $card_number, bool $is_deposit = true, int $fee = 0): array;

    public function today_total_amount(int $card_number): int;

    public function getAccountId(int $cardNumber): int;

    public function getUserId(int $cardNumber): int;

    public function hasBalance(int $cardId, int $amount): bool;
}

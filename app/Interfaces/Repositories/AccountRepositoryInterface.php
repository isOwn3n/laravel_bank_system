<?php

namespace App\Interfaces\Repositories;

interface AccountRepositoryInterface
{
    public function update_balance(int $amount, int $card_number, int $user_id, bool $is_deposit = true): array;

    public function today_total_amount(int $card_number): int;
}

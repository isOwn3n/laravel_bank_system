<?php

namespace App\Interfaces\Repositories;

interface UserRepositoryInterface
{
    public function update_balance(int $amount, int $user_id, bool $is_deposit, int $fee = 0): array;

    public function getBalance(int $userId);
}

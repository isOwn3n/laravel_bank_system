<?php

namespace App\Interfaces\Repositories;

interface UserRepositoryInterface
{
    public function update_balance(int $amount, int $user_id, bool $is_deposit): array;
}

<?php

namespace App\Services;

use App\Repositories\AccountRepository;

class AccountService
{
    public function __construct(
        public AccountRepository $repository,
    ) {}

    public function update_balance(int $amount, int $card_number, int $user_id, bool $is_deposit = true): array
    {
        if ($this->repository->is_account_able($card_number, $amount))
            return $this->repository->update_balance($amount, $card_number, $user_id, $is_deposit);
    }

    public function get_total_transactions_of_day(int $card_number): int
    {
        return $this->repository->today_total_amount($card_number);
    }
}

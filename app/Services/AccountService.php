<?php

namespace App\Services;

use App\Repositories\AccountRepository;

class AccountService
{
    public function __construct(
        protected AccountRepository $repository,
        protected UserService $userService,
    ) {}

    /**
     * @param int $amount
     * @param int $card_number
     * @param int $user_id
     * @param bool $is_deposit
     * @param int $fee
     * @return ?array
     */
    public function update_balance(int $amount, int $card_number, int $user_id, bool $is_deposit = true, int $fee = 0): ?array
    {
        $this->userService->update_balance($amount, $user_id, $is_deposit, $fee);
        return $this->repository->update_balance($amount, $card_number, $is_deposit, $fee);
    }

    /**
     * @param int $card_number
     * @return int
     */
    public function get_total_transactions_of_day(int $card_number): int
    {
        return $this->repository->today_total_amount($card_number);
    }

    /**
     * This is a simple function to get account id by card number.
     * @param int $cardNumber
     * @return int
     */
    public function getAccountId(int $cardNumber): int
    {
        return $this->repository->getAccountId($cardNumber);
    }

    /**
     * This is a simple function to get user id by card number.
     * @param int $cardNumber
     * @return int
     */
    public function getUserIdByAccount(int $cardNumber): int
    {
        return $this->repository->getUserId($cardNumber);
    }
}

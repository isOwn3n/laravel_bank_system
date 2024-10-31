<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected UserRepository $repository
    ) {}

    /**
     * This is a function to call update_balance function from user repository
     */
    public function update_balance(int $amount, int $user_id, bool $is_deposit, int $fee = 0): array
    {
        return $this->repository->update_balance($amount, $user_id, $is_deposit, $fee);
    }

    public function getBalance(int $userId): array
    {
        $user = $this->repository->getUserAndAccoutns($userId);

        $cardsBalance = [];
        $cardsBalance['total'] = $user->balance;

        foreach ($user->accounts as $account) {
            $cardNumber = $account->card_number;
            $cardsBalance['cards'][$cardNumber] = $account->balance;
        }

        return $cardsBalance;
    }
}

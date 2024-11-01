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
     * A function to get balance of user and it cards.
     * @param int $userId
     *
     * @return array
     */
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

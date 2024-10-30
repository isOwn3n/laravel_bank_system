<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public UserRepository $repository
    ) {}

    public function update_balance(int $amount, int $user_id, bool $is_deposit): array
    {
        return $this->repository->update_balance($amount, $user_id, $is_deposit);
    }
}

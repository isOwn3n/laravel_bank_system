<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * A function to get user an it accounts.
     * @param int $userId
     *
     * @return User|null
     */
    public function getUserAndAccoutns(int $userId): ?User
    {
        return $this->model->with('accounts')->where('id', $userId)->first();
    }
}

<?php

namespace App\Interfaces\Repositories;

interface UserRepositoryInterface
{
    public function getBalance(int $userId);
}

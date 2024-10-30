<?php

namespace App\Providers;

use App\Interfaces\Repositories\AccountRepositoryInterface;
use App\Interfaces\Repositories\TransactionRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\Transaction;
use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, function ($app) {
            return new TransactionRepository(
                $app->make(Transaction::class),
                $app->make(UserRepository::class),
                $app->make(AccountRepository::class)
            );
        });
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

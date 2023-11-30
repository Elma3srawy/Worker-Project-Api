<?php

namespace App\Providers;

use App\Interface\OrderServiceInterface;
use App\Repository\OrderServiceRepo;
use Illuminate\Support\ServiceProvider;

class RepoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(OrderServiceInterface::class , OrderServiceRepo::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

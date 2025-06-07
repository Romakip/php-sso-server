<?php

namespace App\Providers;

use App\Contracts\EventDispatcherInterface;
use App\Services\NatsPublisherService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EventDispatcherInterface::class, NatsPublisherService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

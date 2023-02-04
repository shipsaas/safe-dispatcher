<?php

namespace SaasSafeDispatcher;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use SaasSafeDispatcher\Bus\SafeDispatcher;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;

class SafeDispatcherServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        AboutCommand::add('ShipSaaS/SafeDispatcher', fn () => ['Version' => '1.0.0']);

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/safe-dispatcher-routes.php');
    }

    public function register(): void
    {
        $this->app->bind(SafeDispatcher::class, function ($app) {
            return new SafeDispatcher($app, function ($connection = null) use ($app) {
                return $app[QueueFactoryContract::class]->connection($connection);
            });
        });
    }

    public function provides(): array
    {
        return [
            SafeDispatcher::class,
        ];
    }
}

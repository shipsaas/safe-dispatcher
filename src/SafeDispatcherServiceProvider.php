<?php

namespace SaasSafeDispatcher;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SaasSafeDispatcher\Bus\SafeDispatcher;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use SaasSafeDispatcher\Models\FailedToDispatchJob;

class SafeDispatcherServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        AboutCommand::add('ShipSaaS/SafeDispatcher', fn () => ['Version' => '1.2.0']);

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->mergeConfigFrom(__DIR__ . '/Configs/safe-dispatcher.php', 'safe-dispatcher');

        $this->publishes([
            __DIR__ . '/Configs/safe-dispatcher.php' => config_path('safe-dispatcher.php'),
        ], 'safe-dispatcher');

        $this->loadRoutesFrom(__DIR__ . '/Routes/safe-dispatcher-routes.php');

        Route::model('failedToDispatchJob', FailedToDispatchJob::class);
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

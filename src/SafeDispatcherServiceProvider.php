<?php

namespace SaasSafeDispatcher;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class SafeDispatcherServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        AboutCommand::add('ShipSaaS/SafeDispatcher', fn () => ['Version' => '1.0.0']);

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/safe-dispatcher-routes.php');
    }
}

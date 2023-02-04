<?php

namespace SaasSafeDispatcher\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SaasSafeDispatcher\SafeDispatcherServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;
    use DatabaseTransactions;

    protected function getPackageProviders($app): array
    {
        return [
            SafeDispatcherServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $migrationFiles = [
            __DIR__ . '/../src/Database/Migrations/2023_02_04_104010_create_failed_to_dispatch_jobs.php',
        ];

        foreach ($migrationFiles as $migrationFile) {
            $migrateInstance = include $migrationFile;
            $migrateInstance->up();
        }
    }
}

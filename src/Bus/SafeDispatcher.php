<?php

namespace SaasSafeDispatcher\Bus;

use Illuminate\Bus\Dispatcher;
use Illuminate\Queue\SyncQueue;
use RuntimeException;
use SaasSafeDispatcher\Services\FailDispatcherService;
use Throwable;

class SafeDispatcher extends Dispatcher
{
    protected function pushCommandToQueue($queue, $command)
    {
        try {
            return parent::pushCommandToQueue($queue, $command);
        } catch (Throwable $throwable) {
            $this->container
                ->make(FailDispatcherService::class)
                ->storeFailure($queue, $throwable, $command);

            return null;
        }
    }

    public function dispatchNow($command, $handler = null)
    {
        try {
            return parent::dispatchNow($command, $handler);
        }  catch (Throwable $throwable) {
            $this->container
                ->make(FailDispatcherService::class)
                ->storeFailure(
                    $this->container->get(SyncQueue::class),
                    $throwable,
                    $command
                );

            return null;
        }
    }

    public function batch($jobs)
    {
        throw new RuntimeException('Not supported (just yet)');
    }

    public function chain($jobs)
    {
        throw new RuntimeException('Not supported (just yet)');
    }
}

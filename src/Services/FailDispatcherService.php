<?php

namespace SaasSafeDispatcher\Services;

use Illuminate\Contracts\Queue\Queue;
use Throwable;

class FailDispatcherService
{
    public function storeFailure(
        Queue $queueDriver,
        Throwable $throwable,
        $command
    ): void {

    }

}

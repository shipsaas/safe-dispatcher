<?php

namespace SaasSafeDispatcher\Services;

use Illuminate\Contracts\Queue\Queue;
use SaasSafeDispatcher\Models\FailedToDispatchJob;
use Throwable;

class FailDispatcherService
{
    public function storeFailure(
        Queue $queueDriver,
        Throwable $throwable,
        $command
    ): void {
        FailedToDispatchJob::create([
            'job_class' => get_class($command),
            'job_detail' => serialize($command),
            'queue' => $command->queue,
            'connection' => $queueDriver->getConnectionName(),
            'errors' => [
                'msg' => $throwable->getMessage(),
                'traces' => $throwable->getTrace(),
            ],
        ]);
    }
}

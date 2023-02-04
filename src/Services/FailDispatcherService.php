<?php

namespace SaasSafeDispatcher\Services;

use Illuminate\Contracts\Queue\Queue;
use SaasSafeDispatcher\Bus\SafeDispatcher;
use SaasSafeDispatcher\Models\FailedToDispatchJob;
use Throwable;

class FailDispatcherService
{
    /**
     * Store the failure to dispatch msg to DB in order to retry later
     */
    public function storeFailure(
        Queue $queueDriver,
        Throwable $throwable,
        $command
    ): void {
        FailedToDispatchJob::create([
            'job_class' => get_class($command),
            'job_detail' => serialize($command),
            'queue' => $command->queue ?? null,
            'connection' => $queueDriver->getConnectionName(),
            'errors' => [
                'msg' => $throwable->getMessage(),
                'traces' => $throwable->getTrace(),
            ],
        ]);
    }

    /**
     * Redispatch a specific job
     */
    public function redispatch(
        FailedToDispatchJob $failedToDispatchJob,
        RedispatchOption $option
    ): void {
        $job = unserialize($failedToDispatchJob->job_detail);

        if ($option->connection && method_exists($job, 'onConnection')) {
            $job->onConnection($option->connection);
        }

        if ($option->queue && method_exists($job, 'onQueue')) {
            $job->onQueue($option->queue);
        }

        app(SafeDispatcher::class)->dispatch($job);

        $failedToDispatchJob->touch('redispatched_at');
    }
}

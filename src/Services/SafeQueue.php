<?php

namespace SaasSafeDispatcher\Services;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;
use Throwable;
use DateInterval;
use DateTimeInterface;

class SafeQueue
{
    private function __construct(
        private readonly ShouldQueue $job,
        private readonly ?string $connection = null
    ) {
    }

    public static function prepareFor(ShouldQueue $job, ?string $connection = null): static
    {
        return new SafeQueue($job, $connection);
    }

    private function getQueueConnection(): QueueContract
    {
        return Queue::connection($this->connection);
    }

    private function pushSafety(callable $pushInvoker): void
    {
        try {
            call_user_func($pushInvoker);
        } catch (Throwable $throwable) {
            app(FailDispatcherService::class)
                ->storeFailure(
                    $this->getQueueConnection(),
                    $throwable,
                    $this->job
                );
        }
    }

    public function push(?string $queue = null): void
    {
        $this->pushSafety(
            fn () => $this->getQueueConnection()->push($this->job, queue: $queue)
        );
    }

    public function later(
        DateTimeInterface|DateInterval|int $delay,
        ?string $queue = null
    ): void {
        $this->pushSafety(
            fn () => $this->getQueueConnection()->later($delay, $this->job, queue: $queue)
        );
    }
}

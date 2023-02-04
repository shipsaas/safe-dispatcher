<?php

namespace SaasSafeDispatcher\Tests\Unit;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Connectors\NullConnector;
use Illuminate\Queue\NullQueue;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use SaasSafeDispatcher\Bus\SafeDispatcher;
use SaasSafeDispatcher\Models\FailedToDispatchJob;
use SaasSafeDispatcher\Tests\TestCase;
use SaasSafeDispatcher\Traits\SafeDispatchable;

class SafeDispatcherTest extends TestCase
{
    public function testDispatchQueueOk()
    {
        config([
            'queue.default' => 'redis',
        ]);

        QueueJob::safeDispatch('SafeDispatch');

        $this->assertSame(1, app(QueueManager::class)->connection()->size());

        // works fine
        $this->artisan('queue:work redis --max-jobs=1')->assertOk();

        $this->assertSame(0, app(QueueManager::class)->connection()->size());
    }

    public function testDispatchQueueFailed()
    {
        config([
            'queue.default' => 'null',
        ]);

        $nullQueueDriver = $this->createMock(NullQueue::class);
        $nullQueueDriver->method('setContainer')->willReturnSelf();
        $nullQueueDriver->method('setConnectionName')->willReturnSelf();
        $nullQueueDriver->expects($this->once())
            ->method('push')
            ->willThrowException(new RuntimeException('Cannot dispatch job'));

        app(QueueManager::class)
            ->addConnector('null', fn () => new class ($nullQueueDriver) extends NullConnector {
                public function __construct(public NullQueue $nullQueue)
                {
                }

                public function connect(array $config)
                {
                    return $this->nullQueue;
                }
            });

        QueueJob::safeDispatch('SafeDispatch');

        $this->assertDatabaseHas((new FailedToDispatchJob())->getTable(), [
            'job_class' => QueueJob::class,
            'errors->msg' => 'Cannot dispatch job',
        ]);
    }

    public function testDispatchNowOk()
    {
        $job = new QueueJob('Seth Phat');

        Log::expects('info')
            ->once()
            ->with('Hello Seth Phat');

        app(SafeDispatcher::class)->dispatchSync($job);
    }

    public function testDispatchNowFailed()
    {
        $job = new QueueJob('Seth Phat');

        Log::expects('info')
            ->once()
            ->with('Hello Seth Phat')
            ->andThrow(new RuntimeException('Job Failed to process'));

        // for sync & now, since it will be processed in the same/synchronous process
        // so if the handle failed => consider it a failed to dispatch
        app(SafeDispatcher::class)->dispatchSync($job);

        $this->assertDatabaseHas((new FailedToDispatchJob())->getTable(), [
            'job_class' => QueueJob::class,
            'errors->msg' => 'Job Failed to process',
        ]);
    }

    public function testBatchIsNotSupported()
    {
        $this->expectException(RuntimeException::class);

        app(SafeDispatcher::class)->batch([]);
    }

    public function testChainIsNotSupported()
    {
        $this->expectException(RuntimeException::class);

        app(SafeDispatcher::class)->chain([]);
    }
}

class QueueJob implements ShouldQueue
{
    use SafeDispatchable;

    public function __construct(public string $hello)
    {
    }

    public function handle(): void
    {
        Log::info('Hello ' . $this->hello);
    }
}

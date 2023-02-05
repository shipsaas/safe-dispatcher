<?php

namespace SaasSafeDispatcher\Tests\Integration;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Queue\Connectors\NullConnector;
use Illuminate\Queue\NullQueue;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use SaasSafeDispatcher\Models\FailedToDispatchJob;
use SaasSafeDispatcher\Services\FailDispatcherService;
use SaasSafeDispatcher\Services\RedispatchOption;
use SaasSafeDispatcher\Tests\TestCase;
use SaasSafeDispatcher\Traits\SafeDispatchable;

class RedisQueueTest extends TestCase
{
    public function testDispatchQueueAndWorkOk()
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

    public function testDispatchClosureJobOk()
    {
        config([
            'queue.default' => 'redis',
        ]);

        safeDispatch(function () {
            return 'Hello World';
        });

        $this->assertSame(1, app(QueueManager::class)->connection()->size());

        // works fine
        $this->artisan('queue:work redis --max-jobs=1')->assertOk();

        $this->assertSame(0, app(QueueManager::class)->connection()->size());
    }

    public function testRedispatchFailedToDispatchJobOk()
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

        // 1. Failed to dispatch
        QueueJob::safeDispatch('SafeDispatch');

        $this->assertDatabaseHas((new FailedToDispatchJob())->getTable(), [
            'job_class' => QueueJob::class,
            'errors->msg' => 'Cannot dispatch job',
        ]);

        // 2. Redispatch
        $storedFailedToDispatchJob = FailedToDispatchJob::where([
            'job_class' => QueueJob::class,
        ])->first();

        $jobObject = $storedFailedToDispatchJob?->getJobObject();

        $this->assertNotNull($storedFailedToDispatchJob);
        $this->assertNotNull($jobObject);
        $this->assertInstanceOf(ShouldQueue::class, $jobObject);

        $redispatchOption = new RedispatchOption();
        $redispatchOption->connection = 'redis';
        app(FailDispatcherService::class)->redispatch(
            $storedFailedToDispatchJob,
            $redispatchOption
        );

        $storedFailedToDispatchJob->refresh();
        $this->assertNotNull($storedFailedToDispatchJob->redispatched_at);

        // 3. Final assertion & queue work
        $this->assertSame(1, app(QueueManager::class)->connection('redis')->size());

        $this->artisan('queue:work redis --max-jobs=1')->assertOk();

        $this->assertSame(0, app(QueueManager::class)->connection('redis')->size());
    }

    public function testRedispatchFailedToDispatchClosureJobOk()
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

        // 1. Failed to dispatch
        safeDispatch(function () {
            return 'Hihi';
        });

        $this->assertDatabaseHas((new FailedToDispatchJob())->getTable(), [
            'job_class' => CallQueuedClosure::class,
            'errors->msg' => 'Cannot dispatch job',
        ]);

        // 2. Redispatch
        $storedFailedToDispatchJob = FailedToDispatchJob::where([
            'job_class' => CallQueuedClosure::class,
        ])->first();

        $jobObject = $storedFailedToDispatchJob?->getJobObject();

        $this->assertNotNull($storedFailedToDispatchJob);
        $this->assertNotNull($jobObject);
        $this->assertInstanceOf(ShouldQueue::class, $jobObject);

        $redispatchOption = new RedispatchOption();
        $redispatchOption->connection = 'redis'; // push to another driver
        $redispatchOption->queue = 'high'; // push to another queue too
        app(FailDispatcherService::class)->redispatch(
            $storedFailedToDispatchJob,
            $redispatchOption
        );

        $storedFailedToDispatchJob->refresh();
        $this->assertNotNull($storedFailedToDispatchJob->redispatched_at);

        // 3. Final assertion & queue work
        $this->assertSame(1, app(QueueManager::class)->connection('redis')->size('high'));

        $this->artisan('queue:work redis --queue=high --max-jobs=1')->assertOk();

        $this->assertSame(0, app(QueueManager::class)->connection('redis')->size('high'));
    }
}

class QueueJob implements ShouldQueue
{
    use Queueable;
    use SafeDispatchable;

    public function __construct(public string $hello)
    {
    }

    public function handle(): void
    {
        Log::info('Hello ' . $this->hello);
    }
}

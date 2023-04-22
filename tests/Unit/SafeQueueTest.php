<?php

namespace SaasSafeDispatcher\Tests\Unit;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Connectors\NullConnector;
use Illuminate\Queue\NullQueue;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use SaasSafeDispatcher\Models\FailedToDispatchJob;
use SaasSafeDispatcher\Services\SafeQueue;
use SaasSafeDispatcher\Tests\TestCase;

class SafeQueueTest extends TestCase
{
    public function testInitializeSafeQueue()
    {
        $safeQueue = SafeQueue::prepareFor(new TestJob());
        $this->assertInstanceOf(SafeQueue::class, $safeQueue);
    }

    public function testPushOk()
    {
        Queue::fake([TestJob::class]);

        SafeQueue::prepareFor(new TestJob())->push();

        Queue::assertPushed(TestJob::class);
    }

    public function testPushOnQueueOk()
    {
        Queue::fake([TestJob::class]);

        SafeQueue::prepareFor(new TestJob())->push('test');

        Queue::assertPushedOn('test', TestJob::class);
    }

    public function testPushNotOkWillLog()
    {
        config([
            'queue.default' => 'null',
        ]);

        $nullQueueDriver = $this->createMock(NullQueue::class);
        $nullQueueDriver->method('setContainer')->willReturnSelf();
        $nullQueueDriver->method('setConnectionName')->willReturnSelf();
        $nullQueueDriver->expects($this->once())
            ->method('push')
            ->willThrowException(new RuntimeException('Cannot push job'));

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

        SafeQueue::prepareFor(new TestJob())->push();

        $this->assertDatabaseHas((new FailedToDispatchJob())->getTable(), [
            'queue_connection' => null,
            'queue_name' => null,
            'job_class' => TestJob::class,
            'errors->msg' => 'Cannot push job',
        ]);

    }

    public function testPushLaterOk()
    {
        Queue::fake([TestJob::class]);

        SafeQueue::prepareFor(new TestJob())->later(now()->addSeconds());

        Queue::assertPushed(TestJob::class);
    }

    public function testPushLaterOnQueueOk()
    {
        Queue::fake([TestJob::class]);

        SafeQueue::prepareFor(new TestJob())->later(now()->addSeconds(), 'hehe');

        Queue::assertPushedOn('hehe', TestJob::class);
    }
}

class TestJob implements ShouldQueue
{
}

<?php

namespace SaasSafeDispatcher\Tests\Feature;

use Illuminate\Queue\CallQueuedClosure;
use SaasSafeDispatcher\Models\FailedToDispatchJob;
use SaasSafeDispatcher\Tests\TestCase;

class FailedToDispatchJobControllerTest extends TestCase
{
    public function testIndexShowsAllRecords()
    {
        $jobs = FailedToDispatchJob::factory()
            ->count(2)
            ->create();

        $this->json('GET', route('safe-dispatcher.failed-to-dispatch-jobs.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $jobs[0]->id,
            ])
            ->assertJsonFragment([
                'id' => $jobs[1]->id,
            ]);
    }

    public function testIndexFiltersByJobClass()
    {
        $jobs = FailedToDispatchJob::factory()
            ->count(2)
            ->sequence(
                [
                    'job_class' => 'TestClass',
                ],
                [
                    'job_class' => CallQueuedClosure::class,
                ]
            )->create();

        $this->json('GET', route('safe-dispatcher.failed-to-dispatch-jobs.index'), [
            'job_class' => CallQueuedClosure::class,
        ])
            ->assertOk()
            ->assertJsonMissing([
                'id' => $jobs[0]->id,
            ])
            ->assertJsonFragment([
                'id' => $jobs[1]->id,
            ]);
    }

    public function testIndexFiltersByFailedFrom()
    {
        $jobs = FailedToDispatchJob::factory()
            ->count(2)
            ->sequence(
                [
                    'created_at' => '2023-01-02 10:00:00',
                ],
                [
                    'created_at' => '2023-01-01 11:00:00',
                ]
            )->create();

        $this->json('GET', route('safe-dispatcher.failed-to-dispatch-jobs.index'), [
            'failed_from' => '2023-01-02 00:00:00',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'id' => $jobs[0]->id,
            ])
            ->assertJsonMissing([
                'id' => $jobs[1]->id,
            ]);
    }

    public function testIndexFiltersByFailedTo()
    {
        $jobs = FailedToDispatchJob::factory()
            ->count(3)
            ->sequence(
                [
                    'created_at' => '2023-01-02 10:00:00',
                ],
                [
                    'created_at' => '2023-01-01 11:00:00',
                ],
                [
                    'created_at' => '2023-01-05 12:00:00',
                ]
            )->create();

        $this->json('GET', route('safe-dispatcher.failed-to-dispatch-jobs.index'), [
            'failed_to' => '2023-01-03 00:00:00',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'id' => $jobs[0]->id,
            ])
            ->assertJsonFragment([
                'id' => $jobs[1]->id,
            ])
            ->assertJsonMissing([
                'id' => $jobs[2]->id,
            ]);
    }

    public function testShowReturnsSingleInstance()
    {
        /** @var FailedToDispatchJob $job */
        $job = FailedToDispatchJob::factory()->create();

        $this->json('GET', route('safe-dispatcher.failed-to-dispatch-jobs.show', [$job]))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $job->id,
                'job_class' => $job->job_class,
                'errors' => $job->errors,
            ]);
    }

    public function testRetrySuccessfully()
    {
        /** @var FailedToDispatchJob $job */
        $job = FailedToDispatchJob::factory()->create();

        $this->json('PATCH', route('safe-dispatcher.failed-to-dispatch-jobs.retry', [$job]))
            ->assertOk();

        $job->refresh();

        $this->assertNotNull($job->redispatched_at);
    }
}

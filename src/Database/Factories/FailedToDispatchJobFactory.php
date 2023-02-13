<?php

namespace SaasSafeDispatcher\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use SaasSafeDispatcher\Models\FailedToDispatchJob;

class FailedToDispatchJobFactory extends Factory
{
    protected $model = FailedToDispatchJob::class;

    public function definition(): array
    {
        return [
            'job_class' => CallQueuedClosure::class,
            'queue_connection' => 'sync',
            'queue_name' => null,
            'job_detail' => serialize(CallQueuedClosure::create(fn () => Log::info('hehe'))),
            'errors' => 'Error',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'redispatched_at' => null,
        ];
    }
}

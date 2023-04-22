<?php

use Illuminate\Queue\CallQueuedClosure;
use SaasSafeDispatcher\Bus\SafeDispatcher;
use SaasSafeDispatcher\Bus\SafePendingClosureDispatch;
use SaasSafeDispatcher\Bus\SafePendingDispatch;
use SaasSafeDispatcher\Services\FailDispatcherService;

if (!function_exists('safeDispatch')) {
    /**
     * Helper method - safe dispatch a job
     */
    function safeDispatch($job): SafePendingClosureDispatch|SafePendingDispatch
    {
        return $job instanceof Closure
            ? new SafePendingClosureDispatch(CallQueuedClosure::create($job))
            : new SafePendingDispatch($job);
    }
}

if (!function_exists('safeDispatchSync')) {
    /**
     * Helper method - safely dispatch a job in sync connection
     */
    function safeDispatchSync($job, $handler = null)
    {
        return app(SafeDispatcher::class)->dispatchSync($job, $handler);
    }
}

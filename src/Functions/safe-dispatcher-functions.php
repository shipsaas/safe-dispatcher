<?php

use Illuminate\Queue\CallQueuedClosure;
use SaasSafeDispatcher\Bus\SafeDispatcher;
use SaasSafeDispatcher\Bus\SafePendingClosureDispatch;
use SaasSafeDispatcher\Bus\SafePendingDispatch;

// @codeCoverageIgnoreStart
if (!function_exists('safeDispatch')) {
// @codeCoverageIgnoreEnd
    /**
     * Helper method - safe dispatch a job
     */
    function safeDispatch($job): SafePendingClosureDispatch|SafePendingDispatch
    {
        return $job instanceof Closure
            ? new SafePendingClosureDispatch(CallQueuedClosure::create($job))
            : new SafePendingDispatch($job);
    }
// @codeCoverageIgnoreStart
}
// @codeCoverageIgnoreEnd

// @codeCoverageIgnoreStart
if (!function_exists('safeDispatchSync')) {
// @codeCoverageIgnoreEnd
    /**
     * Helper method - safely dispatch a job in sync connection
     */
    function safeDispatchSync($job, $handler = null)
    {
        return app(SafeDispatcher::class)->dispatchSync($job, $handler);
    }
// @codeCoverageIgnoreStart
}
// @codeCoverageIgnoreEnd

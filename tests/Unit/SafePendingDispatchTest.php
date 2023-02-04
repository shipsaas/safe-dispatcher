<?php

namespace SaasSafeDispatcher\Tests\Unit;

use SaasSafeDispatcher\Bus\SafeDispatcher;
use SaasSafeDispatcher\Bus\SafePendingDispatch;
use SaasSafeDispatcher\Tests\TestCase;

class SafePendingDispatchTest extends TestCase
{
    public function testWontDispatchWhenShouldDispatchReturnsFalse()
    {
        $job = $this->createPartialMock(SafePendingDispatch::class, [
            'shouldDispatch',
        ]);
        $job->method('shouldDispatch')->willReturn(false);

        $safeDispatcher = $this->createMock(SafeDispatcher::class);
        $safeDispatcher->expects($this->never())->method('dispatchAfterResponse');
        $safeDispatcher->expects($this->never())->method('dispatch');

        $this->app->offsetSet(SafeDispatcher::class, $safeDispatcher);

        $job->__destruct();
    }

    public function testDispatchAfterResponse()
    {
        $job = $this->createPartialMock(SafePendingDispatch::class, [
            'shouldDispatch',
        ]);
        $job->method('shouldDispatch')->willReturn(true);
        $job->afterResponse();

        $safeDispatcher = $this->createMock(SafeDispatcher::class);
        $safeDispatcher->expects($this->once())->method('dispatchAfterResponse');
        $safeDispatcher->expects($this->never())->method('dispatch');

        $this->app->offsetSet(SafeDispatcher::class, $safeDispatcher);

        $job->__destruct();
    }
}

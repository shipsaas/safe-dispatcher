<?php

namespace SaasSafeDispatcher\Bus;

use Illuminate\Foundation\Bus\PendingDispatch;

class SafePendingDispatch extends PendingDispatch
{
    public function __destruct()
    {
        if (!$this->shouldDispatch()) {
            return;
        } elseif ($this->afterResponse) {
            app(SafeDispatcher::class)->dispatchAfterResponse($this->job);
        } else {
            app(SafeDispatcher::class)->dispatch($this->job);
        }
    }
}

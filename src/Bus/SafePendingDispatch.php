<?php

namespace SaasSafeDispatcher\Bus;

use Illuminate\Foundation\Bus\PendingDispatch;

class SafePendingDispatch extends PendingDispatch
{
    public function __destruct()
    {
        if (! $this->shouldDispatch()) {
            return;
        } elseif ($this->afterResponse) {
            app(Dispatcher::class)->dispatchAfterResponse($this->job);
        } else {
            app(Dispatcher::class)->dispatch($this->job);
        }
    }
}

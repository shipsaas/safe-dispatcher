<?php

namespace SaasSafeDispatcher\Traits;

use SaasSafeDispatcher\Bus\SafePendingDispatch;

trait SafeDispatchable
{
    /**
     * @see Dispatchable::dispatch() for the inspiration
     */
    public static function safeDispatch(...$arguments): SafePendingDispatch
    {
        return new SafePendingDispatch(new static(...$arguments));
    }
}

<?php

namespace SaasSafeDispatcher\Bus;

use Closure;

class SafePendingClosureDispatch extends SafePendingDispatch
{
    /**
     * Already covered from Laravel
     *
     * @codeCoverageIgnore
     */
    public function catch(Closure $callback)
    {
        $this->job->onFailure($callback);

        return $this;
    }
}

<?php

namespace SaasSafeDispatcher\Services;

/**
 * Redispatch Option class
 */
final class RedispatchOption
{
    /**
     * Put a new value here if you wish to use another queue driver
     */
    public ?string $connection = null;

    /**
     * Put a new value here if you wish to use another queue name
     */
    public ?string $queue = null;
}

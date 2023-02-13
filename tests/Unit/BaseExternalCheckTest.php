<?php

namespace SaasSafeDispatcher\Tests\Unit;

use Illuminate\Foundation\Http\FormRequest;
use SaasSafeDispatcher\Authorizations\FailedToDispatchRetryCheck;
use SaasSafeDispatcher\Tests\TestCase;

class BaseExternalCheckTest extends TestCase
{
    public function testBaseExternalCanCheck()
    {
        FailedToDispatchRetryCheck::setCheck(function () {
            return false;
        });

        $this->assertFalse(FailedToDispatchRetryCheck::authorize(new FormRequest()));

        FailedToDispatchRetryCheck::setCheck(function () {
            return true;
        });

        $this->assertTrue(FailedToDispatchRetryCheck::authorize(new FormRequest()));
    }
}

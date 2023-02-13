<?php

namespace SaasSafeDispatcher\Authorizations;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseExternalCheck
{
    protected static ?Closure $checkMethod = null;

    public static function setCheck(callable $checkMethod): void
    {
        static::$checkMethod = $checkMethod;
    }

    public static function authorize(FormRequest $request): bool
    {
        return is_callable(static::$checkMethod)
            ? call_user_func(static::$checkMethod, $request)
            : true; // by default
    }
}

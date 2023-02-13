<?php

namespace SaasSafeDispatcher\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use SaasSafeDispatcher\Authorizations\FailedToDispatchRetryCheck;

class FailedToDispatchJobRetryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return FailedToDispatchRetryCheck::authorize($this);
    }

    public function rules(): array
    {
        return [];
    }
}

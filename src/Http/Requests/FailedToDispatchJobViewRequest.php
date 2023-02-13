<?php

namespace SaasSafeDispatcher\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use SaasSafeDispatcher\Authorizations\FailedToDispatchViewCheck;

class FailedToDispatchJobViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return FailedToDispatchViewCheck::authorize($this);
    }

    public function rules(): array
    {
        return [];
    }
}

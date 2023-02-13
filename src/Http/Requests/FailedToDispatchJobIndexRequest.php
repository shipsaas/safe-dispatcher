<?php

namespace SaasSafeDispatcher\Http\Requests;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use SaasSafeDispatcher\Authorizations\FailedToDispatchViewCheck;
use SaasSafeDispatcher\Models\FailedToDispatchJob;

class FailedToDispatchJobIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return FailedToDispatchViewCheck::authorize($this);
    }

    public function rules(): array
    {
        return [
            'limit' => 'nullable|integer|min:10|max:100',
            'page' => 'nullable|integer',
            'job_class' => 'nullable|string',
            'failed_from' => [
                'nullable',
                'date_format:Y-m-d H:i:s',
            ],
            'failed_to' => [
                'nullable',
                'date_format:Y-m-d H:i:s',
            ],
            'wants_redispatched' => 'nullable|boolean',
            'sort_by' => [
                'nullable',
                'string',
                Rule::in([
                    'created_at',
                    'job_class',
                ]),
            ],
            'sort_direction' => [
                'nullable',
                'string',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],
        ];
    }

    public function computeQueryBuilder(): Builder
    {
        return FailedToDispatchJob::query()
            ->orderBy(
                $this->input('sort_by') ?: 'created_at',
                $this->input('sort_direction') ?: 'DESC'
            )->when(
                $this->boolean('wants_redispatched'),
                fn ($q) => $q->whereNotNull('redispatched_at')
            )->when(
                $this->filled('job_class'),
                fn ($q) => $q->where('job_class', $this->input('job_class'))
            )->when(
                $this->filled('failed_from'),
                fn ($q) => $q->where('created_at', '>=', $this->input('failed_from'))
            )->when(
                $this->filled('failed_to'),
                fn ($q) => $q->where('created_at', '<=', $this->input('failed_to'))
            );
    }
}

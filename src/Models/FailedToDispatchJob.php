<?php

namespace SaasSafeDispatcher\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FailedToDispatchJob extends Model
{
    use HasUuids;

    protected $table = 'failed_to_dispatch_jobs';

    protected $fillable = [
        'job_class',
        'job_detail',
        'connection',
        'queue',
        'errors',
        'redispatched_at',
    ];

    protected $casts = [
        'errors' => 'array',
    ];

    public function getJobObject(): mixed
    {
        return unserialize($this->job_detail);
    }
}

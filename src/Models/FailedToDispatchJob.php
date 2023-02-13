<?php

namespace SaasSafeDispatcher\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaasSafeDispatcher\Database\Factories\FailedToDispatchJobFactory;

class FailedToDispatchJob extends Model
{
    use HasUuids;
    use HasFactory;

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

    protected static function newFactory(): FailedToDispatchJobFactory
    {
        return FailedToDispatchJobFactory::new();
    }
}

<?php

namespace SaasSafeDispatcher\Http\Resources\Models;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use SaasSafeDispatcher\Models\FailedToDispatchJob;

/** @mixin FailedToDispatchJob */
class FailedToDispatchJobResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'job_class' => $this->job_class,
            'connection' => $this->queue_connection,
            'job_detail' => $this->job_detail,
            'queue' => $this->queue_name,
            'errors' => $this->errors,
            'created_at' => $this->created_at,
            'redispatched_at' => $this->redispatched_at,
        ];
    }
}

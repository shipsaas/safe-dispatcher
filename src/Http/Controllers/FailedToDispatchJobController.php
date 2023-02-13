<?php

namespace SaasSafeDispatcher\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use SaasSafeDispatcher\Http\Requests\FailedToDispatchJobIndexRequest;
use SaasSafeDispatcher\Http\Requests\FailedToDispatchJobRetryRequest;
use SaasSafeDispatcher\Http\Requests\FailedToDispatchJobViewRequest;
use SaasSafeDispatcher\Http\Resources\Models\FailedToDispatchJobResource;
use SaasSafeDispatcher\Models\FailedToDispatchJob;
use SaasSafeDispatcher\Services\FailDispatcherService;

class FailedToDispatchJobController extends Controller
{
    public function index(FailedToDispatchJobIndexRequest $request): JsonResponse
    {
        $query = $request->computeQueryBuilder()
            ->paginate($request->integer('limit') ?: 10);

        return FailedToDispatchJobResource::collection($query)->response();
    }

    public function show(
        FailedToDispatchJobViewRequest $request,
        FailedToDispatchJob $failedToDispatchJob
    ): JsonResponse {
        return (new FailedToDispatchJobResource($failedToDispatchJob))->response();
    }

    public function retry(
        FailedToDispatchJobRetryRequest $request,
        FailedToDispatchJob $failedToDispatchJob,
        FailDispatcherService $failDispatcherService
    ): JsonResponse {
        $failDispatcherService->redispatch($failedToDispatchJob);

        return new JsonResponse(['success' => true]);
    }
}

<?php

use Illuminate\Support\Facades\Route;
use SaasSafeDispatcher\Http\Controllers\FailedToDispatchJobController;
use Illuminate\Routing\Middleware\SubstituteBindings;

if (config('safe-dispatcher.uses_apis')) {
    Route::prefix(config('safe-dispatcher.api_prefix_route'))
        ->name('safe-dispatcher.')
        ->middleware([
            SubstituteBindings::class,
            ...config('safe-dispatcher.api_middlewares'),
        ])
        ->group(function () {
            Route::get('/failed-to-dispatch-jobs', [FailedToDispatchJobController::class, 'index'])
                ->name('failed-to-dispatch-jobs.index');
            Route::get('/failed-to-dispatch-jobs/{failedToDispatchJob}', [FailedToDispatchJobController::class, 'show'])
                ->name('failed-to-dispatch-jobs.show');
            Route::patch('/failed-to-dispatch-jobs/{failedToDispatchJob}', [FailedToDispatchJobController::class, 'retry'])
                ->name('failed-to-dispatch-jobs.retry');
        });
}

<?php

if (config('safe-dispatcher.uses_apis')) {
    Route::prefix(config('safe-dispatcher.api_prefix_route'))
        ->middleware(config('safe-dispatcher.api_middlewares'))
        ->group(function () {
            Route::get('/failed-to-dispatch-jobs', []);
            Route::get('/failed-to-dispatch-jobs/{failedToDispatchJob}', []);
            Route::patch('/failed-to-dispatch-jobs/{failedToDispatchJob', []);
        });
}

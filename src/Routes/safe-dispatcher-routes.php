<?php

if (config('safe-dispatcher.uses_internal_dash')) {
    Route::prefix(config('safe-dispatcher.internal_dash_prefix_route'))
        ->middleware(config('safe-dispatcher.internal_dash_middlewares'))
        ->group(function () {
        });
}

if (config('safe-dispatcher.uses_apis')) {
    Route::prefix(config('safe-dispatcher.api_prefix_route'))
        ->middleware(config('safe-dispatcher.api_middlewares'))
        ->group(function () {
        });
}

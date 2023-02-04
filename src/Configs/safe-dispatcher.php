<?php

return [
    /**
     * We ship SafeDispatcher with some REST APIs in case that you
     * don't want to build your own.
     */
    'uses_apis' => true,

    /**
     * Prefix of the existing APIs
     */
    'api_prefix_route' => 'safe-dispatcher-apis',

    /**
     * List of middlewares (for permissions checking) when accessing the APIs
     *
     * eg: ['web', MyMiddleware::class]
     */
    'api_middlewares' => [],
];

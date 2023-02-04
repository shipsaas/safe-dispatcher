<?php

return [
    /**
     * We have a built-in internal dashboard, if you don't want to use it
     *
     * Mark this one as false
     */
    'uses_internal_dash' => true,

    /**
     * Prefix of the internal dash route
     */
    'internal_dash_prefix_route' => 'safe-dispatcher-dashboard',

    /**
     * List of middlewares (for permissions checking) when accessing the
     * internal dash
     *
     * eg: ['web', MyMiddleware::class]
     */
    'internal_dash_middlewares' => [],

    /**
     * We also ship SafeDispatcher with some REST APIs in case that you
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

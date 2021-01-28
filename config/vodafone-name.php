<?php

return [
    /**
     * Enable and disable logging and debug mode
     */
    'debug-mode'          => env('NAME_DEBUG_MODE', false),

    /**
     * return static response if not prod
     */
    'testing-mode'        => env('NAME_TESTING_MODE', false),

    /**
     * Destinition address of middleware databas
     */
    'middleware-host'     => env('NAME_MIDDLEWARE_HOST', 'https://dspmw.vodafone.ua'),

    /**
     * Middleware connection user login
     */
    'middleware-login'    => env('NAME_MIDDLEWARE_LOGIN', null),

    /**
     * Middleware connection user password
     */
    'middleware-password' => env('NAME_MIDDLEWARE_PASSWORD', null),

    /**
     * Middleware profile
     */
    'middleware-profile'  => env('NAME_MIDDLEWARE_PROFILE', 'NAME-RTM'),

    /**
     * Middleware channel
     */
    'middleware-channel' => env('NAME_MIDDLEWARE_CHANNEL', 'VF-WEBSITE'),

    /**
     * Support language for NameService
     */
    'support_languages'   => [
        'uk', 'ru'
    ],
];

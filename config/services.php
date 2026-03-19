<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'countries_api' => [
        // REST Countries v3.1 requires ?fields=… (max 10 fields) or returns 400.
        'url' => env(
            'COUNTRIES_API_URL',
            'https://restcountries.com/v3.1/all?fields=name,flags'
        ),
        'timeout' => (int) env('COUNTRIES_API_TIMEOUT', 15),
        'retry_times' => (int) env('COUNTRIES_API_RETRY_TIMES', 2),
    ],

    'countries_cache_ttl_seconds' => (int) env('COUNTRIES_CACHE_TTL_SECONDS', 3600),

];

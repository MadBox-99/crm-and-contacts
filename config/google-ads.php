<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Google Ads API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Ads API integration. You need to obtain these
    | credentials from Google Ads API Center.
    |
    */

    'developer_token' => env('GOOGLE_ADS_DEVELOPER_TOKEN'),

    'client_id' => env('GOOGLE_ADS_CLIENT_ID'),

    'client_secret' => env('GOOGLE_ADS_CLIENT_SECRET'),

    'refresh_token' => env('GOOGLE_ADS_REFRESH_TOKEN'),

    'login_customer_id' => env('GOOGLE_ADS_LOGIN_CUSTOMER_ID'),

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | The version of Google Ads API to use
    |
    */

    'api_version' => env('GOOGLE_ADS_API_VERSION', 'v18'),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable or disable Google Ads API logging
    |
    */

    'logging' => [
        'enabled' => env('GOOGLE_ADS_LOGGING_ENABLED', false),
        'level' => env('GOOGLE_ADS_LOGGING_LEVEL', 'INFO'),
        'file' => storage_path('logs/google-ads.log'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for API requests
    |
    */

    'rate_limit' => [
        'enabled' => env('GOOGLE_ADS_RATE_LIMIT_ENABLED', true),
        'max_attempts' => env('GOOGLE_ADS_RATE_LIMIT_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('GOOGLE_ADS_RATE_LIMIT_DECAY_MINUTES', 1),
    ],
];

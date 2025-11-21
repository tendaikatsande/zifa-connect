<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FIFA Connect API Configuration
    |--------------------------------------------------------------------------
    */

    'api_url' => env('FIFA_CONNECT_API_URL'),
    'api_key' => env('FIFA_CONNECT_API_KEY'),
    'client_id' => env('FIFA_CONNECT_CLIENT_ID'),
    'client_secret' => env('FIFA_CONNECT_CLIENT_SECRET'),

    'sync' => [
        'enabled' => env('FIFA_SYNC_ENABLED', false),
        'batch_size' => 100,
        'retry_attempts' => 3,
        'retry_delay' => 300, // seconds
    ],

    'entities' => [
        'players' => true,
        'clubs' => true,
        'transfers' => true,
        'officials' => true,
    ],
];

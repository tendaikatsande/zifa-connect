<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PesePay Integration Configuration
    |--------------------------------------------------------------------------
    */

    'integration_key' => env('PESEPAY_INTEGRATION_KEY'),
    'encryption_key' => env('PESEPAY_ENCRYPTION_KEY'),
    'base_url' => env('PESEPAY_BASE_URL', 'https://api.pesepay.com/api/payments-engine/v1'),
    'return_url' => env('PESEPAY_RETURN_URL'),
    'result_url' => env('PESEPAY_RESULT_URL'),

    'currency' => env('PESEPAY_DEFAULT_CURRENCY', 'USD'),

    'payment_methods' => [
        'ecocash' => 'EcoCash',
        'onemoney' => 'OneMoney',
        'telecash' => 'Telecash',
        'visa' => 'Visa',
        'mastercard' => 'Mastercard',
        'zipit' => 'ZIPIT',
        'bank_transfer' => 'Bank Transfer',
    ],

    'webhook_secret' => env('PESEPAY_WEBHOOK_SECRET'),

    'retry' => [
        'max_attempts' => 3,
        'delay_seconds' => 5,
    ],
];

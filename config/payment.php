<?php

return [
    'default_gateway' => env('PAYMENT_GATEWAY', 'stripe'),

    'currency' => env('APP_DEFAULT_CURRENCY', 'MAD'),

    'gateways' => [
        'stripe' => [
            'driver' => 'stripe',
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
        'paypal' => [
            'driver' => 'paypal',
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'mode' => env('PAYPAL_MODE', 'sandbox'),
        ],
        'cmi' => [
            'driver' => 'cmi',
            'merchant_id' => env('CMI_MERCHANT_ID'),
            'store_key' => env('CMI_STORE_KEY'),
        ],
    ],
];

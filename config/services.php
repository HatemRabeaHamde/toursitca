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

    'admin' => [
        'name' => env('ADMIN_NAME', 'Platform Admin'),
        'email' => env('ADMIN_EMAIL', 'admin@morocco-tourism.test'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    'demo_access' => [
        'admin_name' => env('DEMO_ADMIN_NAME', 'TourstiCa Admin'),
        'admin_email' => env('DEMO_ADMIN_EMAIL', 'admin@tourstica.test'),
        'admin_password' => env('DEMO_ADMIN_PASSWORD'),
        'agency_owner_name' => env('DEMO_AGENCY_OWNER_NAME', 'TourstiCa Agency Owner'),
        'agency_email' => env('DEMO_AGENCY_EMAIL', 'agency@tourstica.test'),
        'agency_password' => env('DEMO_AGENCY_PASSWORD'),
        'agency_name' => env('DEMO_AGENCY_NAME', 'TourstiCa Local Experiences'),
        'client_name' => env('DEMO_CLIENT_NAME', 'TourstiCa Demo Traveler'),
        'client_email' => env('DEMO_CLIENT_EMAIL', 'client@tourstica.test'),
        'client_password' => env('DEMO_CLIENT_PASSWORD'),
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

];

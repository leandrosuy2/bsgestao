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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    // Sicredi API
    'sicredi' => [
        'api_url' => env('SICREDI_API_URL', 'https://api.sicredi.com.br'),
        // Os dados client_id, client_secret e x_api_key são buscados do banco (UserPaymentIntegration), mas pode deixar aqui para fallback/teste
        'client_id' => env('SICREDI_CLIENT_ID'),
        'client_secret' => env('SICREDI_CLIENT_SECRET'),
        'x_api_key' => env('SICREDI_X_API_KEY'),
    ],

    // Focus NFe API
    'focus_nfe' => [
        'url' => env('FOCUS_NFE_URL', 'https://api.focusnfe.com.br'),
        'token' => env('FOCUS_NFE_TOKEN'),
        'environment' => env('FOCUS_NFE_ENVIRONMENT', 'sandbox'),
    ],
];

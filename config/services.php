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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'svgate' => [
        'url' => env('SVGATE_URL'),
        'url_dev' => env('SVGATE_URL_DEV'),
        'login' => env('SVGATE_LOGIN'),
        'password' => env('SVGATE_PASSWORD')
    ],
    'humo' => [
        'url' => env('HUMO36_URL'),
        'token36' => env('HUMO36_TOKEN'),
        'url_77' => env('HUMO77_URL'),
        'token77' => env('HUMO77_TOKEN'),
        'url_mi' => env('HUMO_MI_HOST'),
        'token_mi' => env('HUMO_MI_TOKEN'),
    ],
    'unipos' => [
        'url' => env('UNIPOS_URL'),
        'username' => env('UNIPOS_USERNAME'),
        'password' => env('UNIPOS_PASSWORD'),
        'secret' => env('UNIPOS_SECRET'),
    ],
    'egov' => [
        'url' => env('EGOV_LOGIN_URL'),
        'token' => env('EGOV_TOKEN'),
    ]

];

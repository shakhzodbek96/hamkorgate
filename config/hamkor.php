<?php
return [
    'base_url'        => env('HAMKOR_BASE_URL', 'https://test-openapi.hamkorbank.uz'),
    'token_endpoint'  => '/token',
    'key'             => env('HAMKOR_KEY'),
    'secret'          => env('HAMKOR_SECRET'),
    'timeout'         => (int) env('HAMKOR_TIMEOUT', 15),
    'connect_timeout' => (int) env('HAMKOR_CONNECT_TIMEOUT', 5),
    'mtls' => [
        'enabled'   => filter_var(env('HAMKOR_MTLS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'cert_path' => env('HAMKOR_MTLS_CERT'),
        'key_path'  => env('HAMKOR_MTLS_KEY'),
        'key_pass'  => env('HAMKOR_MTLS_KEY_PASS'),
        'ca_path'   => env('HAMKOR_MTLS_CA'),   // optional CA bundle
    ],
];

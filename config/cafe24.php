<?php

return [
    // cafe24 app
    'client_id' => env('CAFE24_APP_CLIENT_ID') ?? 'client_id',
    'client_secret' => env('CAFE24_APP_CLIENT_SECRET') ?? 'client_secret',
    'redirect_uri' => env('CAFE24_APP_REDIRECT_URI') ?? 'redirect_uri',
    'app_code' => env('CAFE24_APP_CODE') ?? 'app_code',
    'scope' => env('CAFE24_APP_SCOPE') ?? 'mall.read_application,mall.write_application',
    'state' => env('CAFE24_APP_STATE') ?? 'state',
    'api_domain' => env('CAFE24_API_DOMAIN') ?? 'api_domain',
    'api_admin_prefix' => env('CAFE24_ADMIN_API_PREFIX') ?? 'api_admin_prefix',
    'api_version' => env('CAFE24_API_VERSION') ?? 'api_version',

    //  text const cafe24
    'text_status' => [
        'token_expired' => 'access_token time expired.',
        'invalid_token' => 'Invalid access_token',
    ],

    // random string
    'random_string' => 'randomstring(0123456789)',

    // url login default
    'url' => [
        'login' => 'https://cafe24shop.com/login',
    ],
    'per_page' => 20,
    'host' => env('CAFE24_HOST') ?? 'host',
];

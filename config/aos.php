<?php

return [
    'domain_api' => env('AOS_API_URL') ?? 'domain_api',
    'login' => [
        'authorization' => env('AOS_AUTH') ?? 'authorization',
    ],
    'mall_id' => env('CAFE24_MALL_ID') ?? 'mall_id',

    // mall code orders
    'mall_code' => 'AOS',

    'tax' => [
        'taxation' => 'A',
        'tax_free' => 'B',
        'tax_zero' => 'C',
    ],
    'url_token' => env('URL_TOKEN_AOS') ?? 'url_token',
    'auth_send_token' => env('AUTH_TOKEN_AOS') ?? 'auth_send_token',
    'refresh_key' => env('REFRESH_KEY') ?? 'refresh_key',

    'product_type' => [
        'industrial' => [
            'icon' => 'https://aos.app4cafe24.com/img/icon/box1.png',
            'note' => '기본 포장 : 종이',
        ],
        'miscellaneous' => [
            'icon' => 'https://aos.app4cafe24.com/img/icon/box2.png',
            'note' => '기본 포장 : 종이',
        ],
        'alcoholic' => [
            'icon' => 'https://aos.app4cafe24.com/img/icon/both.svg',
            'note' => '기본 포장 : 종이',
        ]
    ],

    // list code Menu
    'menu' => ['LC', 'MC', 'SC', 'UC'],

    // allow role type
    'role_type' => [2],

    // regular date
    'regular_date' => ['Mon', 'Tue', 'Web', 'Thu', 'Fri', 'Sat', 'Sun'],

    // regular type
    'is_regular' => [
        0, // not regular
        1, // is regular
    ],

    // shipping method
    'shipping_method' => [
        1, // center pickup
        2, // dilivery
        3, // default
    ],

    // page size
    'page_size' => 20,

    // order
    'order' => [
        'carrier_id' => 2, // Self delivery
        'shipping_company_code' => '0001',
    ],

    // send email
    'send_email' => env('SEND_EMAIL') ?? 'F',

    // payment (nice pay)
    'payment' => [
        'mid' => env('NICE_MID') ?? 'mid',
        'moid' => env('NICE_MOID') ?? 'moid',
        'merchant_key' => env('NICE_MERCHANT_KEY') ?? 'merchant_key',
    ],
];

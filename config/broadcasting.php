<?php

return [
    'driver' => env('BROADCAST_DRIVER', 'reverb'),

    'connections' => [
        'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),
            'options' => [
                'cluster' => env('REVERB_APP_CLUSTER'),
                'useTLS' => env('REVERB_USE_TLS', true),
                'host' => env('REVERB_HOST', 'localhost'),
                'port' => env('REVERB_PORT', 443),
                'scheme' => 'https',
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ],
            ],
            'client_options' => [
                'scheme' => env('REVERB_SCHEME', 'wss'),
                'host' => env('REVERB_HOST', 'localhost'),
                'port' => env('REVERB_PORT', 443),
                'path' => '/app/' . env('REVERB_APP_KEY'),
            ],
        ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'host' => env('PUSHER_HOST'),
                'port' => env('PUSHER_PORT', 443),
                'scheme' => 'https',
                'encrypted' => true,
                'useTLS' => true,
            ],
            'client_options' => [
                'scheme' => 'wss',
                'host' => env('PUSHER_HOST', 'ws-mt1.pusher.com'),
                'port' => env('PUSHER_PORT', 443),
                'path' => '/app/' . env('PUSHER_APP_KEY'),
                'query' => ['protocol' => 7, 'client' => 'laravel'],
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'null' => [
            'driver' => 'null',
        ],

        'log' => [
            'driver' => 'log',
        ],
    ],
];

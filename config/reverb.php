<?php

return [
    'apps' => [
        [
            'key' => env('REVERB_APP_KEY', 'kiava-hr-app'),
            'secret' => env('REVERB_APP_SECRET', 'kiava-hr-secret'),
            'app_id' => env('REVERB_APP_ID', '1'),
            'options' => [
                'cluster' => env('REVERB_APP_CLUSTER', 'mt1'),
            ],
            'capacity' => null,
            'encryption' => true,
        ],
    ],

    'apps_manager' => [
        'driver' => 'array',
    ],

    'metrics' => [
        'driver' => 'null',
    ],

    'servers' => [
        [
            'host' => env('REVERB_SERVER_HOST', '0.0.0.0'),
            'port' => env('REVERB_SERVER_PORT', 8080),
            'hostname' => env('REVERB_SERVER_HOSTNAME'),
        ],
    ],

    'localhost_mode' => env('REVERB_LOCALHOST_MODE', false),
];

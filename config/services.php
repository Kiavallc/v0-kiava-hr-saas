<?php

return [
    'stripe' => [
        'public' => env('STRIPE_PUBLIC_KEY'),
        'secret' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'webhook_tolerance' => 300, // 5 minutes
    ],
];

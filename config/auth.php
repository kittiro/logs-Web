<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'file',
        ],
    ],

    'providers' => [
        'file' => [
            'driver' => 'file',
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'file',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];

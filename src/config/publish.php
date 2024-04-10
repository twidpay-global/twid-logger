<?php

use Monolog\Level;
use Monolog\Logger as MonologLogger;

return [
    'channels' => [
        'default' => [
            'driver' => 'daily',
            'path' => 'storage/logs/info.log',
            'level' => Level::Info,
        ],
        'inbound' => [
            'driver' => 'daily',
            'path' => 'storage/logs/inbound.log',
            'level' => Level::Info,
        ],
        'outbound' => [
            'driver' => 'daily',
            'path' => 'storage/logs/outbound.log',
            'level' => Level::Info,
        ],
        'info' => [
            'driver' => 'daily',
            'path' => 'storage/logs/info.log',
            'level' => Level::Info,
        ],
        'critical' => [
            'driver' => 'daily',
            'path' => 'storage/logs/critical.log',
            'level' => Level::Critical,
        ],
        'warning' => [
            'driver' => 'daily',
            'path' => 'storage/logs/warning.log',
            'level' => Level::Warning,
        ],
        'debug' => [
            'driver' => 'daily',
            'path' => 'storage/logs/debug.log',
            'level' => Level::Debug,
        ],
        'alert' => [
            'driver' => 'daily',
            'path' => 'storage/logs/alert.log',
            'level' => Level::Alert,
        ],
        'error' => [
            'driver' => 'daily',
            'path' => 'storage/logs/error.log',
            'level' => Level::Error,
        ],
    ],
    'metadata' => ['user_id', 'ip_address'],
    'mask' => [
        'mobile_number',
        'password'
    ]
];

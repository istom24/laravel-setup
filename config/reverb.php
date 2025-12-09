<?php

return [

    'apps' => [
        [
            'id' => env('REVERB_APP_ID', 'taskflow'),
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'ping_interval' => 60,
            'max_message_size' => 10000,
        ],
    ],

    'options' => [
        'host' => env('REVERB_HOST', '0.0.0.0'),
        'port' => env('REVERB_PORT', 8090),
        'scheme' => env('REVERB_SCHEME', 'http'),
        'debug' => true,
    ],

];

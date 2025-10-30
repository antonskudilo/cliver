<?php

return [
    'connection' => env('DB_CONNECTION', 'csv'),

    'csv' => [
        'path' => env('CSV_PATH', __DIR__ . '/../data'),
    ],

    'db' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'test'),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
];

<?php

use App\DataSource\CompositeCsvDataSource;
use App\DataSource\DatabaseDataSource;

return [
    'orders' => [
        new CompositeCsvDataSource(
            directory: 'orders',
            pattern: 'orders_%s.csv',
            fileKeyField: 'date',
        ),
        DatabaseDataSource::class,
    ],
];

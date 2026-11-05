<?php
return [
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'Phphone',
        'env' => $_ENV['APP_ENV'] ?? 'production',
        'orientation' => $_ENV['APP_ORIENTATION'] ?? 'portrait', // Supports: 'portrait', 'landscape', 'any'
    ],
    'database' => [
        'default' => $_ENV['DB_CONNECTION'] ?? 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => $_ENV['DB_DATABASE'] ?? __DIR__ . '/database.sqlite',
            ],
            'mysql' => [
                'driver' => 'mysql',
                'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'database' => $_ENV['DB_DATABASE'] ?? 'phphone',
                'username' => $_ENV['DB_USERNAME'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
            ],
            'pgsql' => [
                'driver' => 'pgsql',
                'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
                'port' => $_ENV['DB_PORT'] ?? '5432',
                'database' => $_ENV['DB_DATABASE'] ?? 'phphone',
                'username' => $_ENV['DB_USERNAME'] ?? 'postgres',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
            ],
        ],
    ],
];

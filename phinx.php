<?php

require_once __DIR__ . '/config/config.php'; 

return [
    'paths' => [
        'migrations' => 'db/migrations',
        'seeds' => 'db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'name' => getenv('DB_NAME') ?: 'tecnofit',
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASS') ?: '',
            'port' => getenv('DB_PORT') ?: '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];

<?php

use Illuminate\Support\Str;

return [
    'default' => env('DB_CONNECTION', 'mysql2'),

    'connections' => [

    'mysql2' => [
        'driver' => 'mysql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST_2', env('DB_HOST', 'eudr_db_container')),
        'port' => env('DB_PORT_2', env('DB_PORT', '3306')),
        'database' => env('DB_DATABASE_2', env('DB_DATABASE', 'eudr')),
        'username' => env('DB_USERNAME_2', env('DB_USERNAME', 'itadmin')),
        'password' => env('DB_PASSWORD_2', env('DB_PASSWORD', 'ithvf-69')),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ]) : [],
    ],

],

'migrations' => 'migrations',

    
];

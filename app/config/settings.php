<?php

error_reporting(0);
ini_set('display_errors', '0');

date_default_timezone_set('America/Sao_Paulo');

$settings = [];

$settings['root'] = dirname(__DIR__);

$settings['error'] = [
    'display_error_details' => false,
    'log_errors' => true,
    'log_error_details' => true,
];

$settings['db'] = [
    'driver' => 'mariadb',
    'host' => 'mariadb',
    'username' => 'root',
    'database' => 'banking_account',
    'password' => 'admin',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'flags' => [
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
    ],
];

$settings['redis'] = [
    'server' => 'tcp://redis',
    'options' => null,
];

return $settings;
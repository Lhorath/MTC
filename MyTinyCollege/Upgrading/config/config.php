<?php
/**
 * Copy and adjust for your environment. Uses MySQL as required for the PHP port.
 */
return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=mytinycollege;charset=utf8mb4',
        'user' => 'website',
        'pass' => 'dax123',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
    'app' => [
        'base_url' => '', // e.g. '/Upgrading/public' if not at domain root
        'name' => 'My TinyCollege',
    ],
];

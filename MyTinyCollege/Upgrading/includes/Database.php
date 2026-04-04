<?php

declare(strict_types=1);

namespace App\Includes;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }
        /** @var array{db: array{dsn: string, user: string, pass: string, options?: array<int, mixed>}} $config */
        $config = require dirname(__DIR__) . '/config/config.php';
        $db = $config['db'];
        self::$pdo = new PDO($db['dsn'], $db['user'], $db['pass'], $db['options'] ?? []);
        return self::$pdo;
    }
}

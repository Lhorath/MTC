<?php

declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/SecurityHeaders.php';
require_once dirname(__DIR__) . '/includes/Csrf.php';
require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/Auth.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $rel = str_replace('\\', '/', substr($class, strlen($prefix)));
    $base = dirname(__DIR__) . '/src/';
    $file = $base . $rel . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

use App\Includes\SecurityHeaders;

SecurityHeaders::send();

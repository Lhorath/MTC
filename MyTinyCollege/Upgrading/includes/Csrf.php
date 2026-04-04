<?php

declare(strict_types=1);

namespace App\Includes;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function field(): string
    {
        $t = self::token();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $sent): bool
    {
        if ($sent === null || $sent === '') {
            return false;
        }
        $expected = $_SESSION['_csrf'] ?? '';
        return is_string($expected) && hash_equals($expected, $sent);
    }
}

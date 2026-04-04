<?php

declare(strict_types=1);

namespace App\Includes;

final class Auth
{
    public static function userId(): ?int
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        return (int) $_SESSION['user_id'];
    }

    public static function userEmail(): ?string
    {
        return isset($_SESSION['user_email']) ? (string) $_SESSION['user_email'] : null;
    }

    public static function check(): bool
    {
        return self::userId() !== null;
    }

    public static function login(int $userId, string $email): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
        }
        session_destroy();
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            \redirect('account/login', ['ReturnUrl' => $_SERVER['REQUEST_URI'] ?? '']);
        }
    }
}

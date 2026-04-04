<?php

declare(strict_types=1);

function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * URL path to the directory that contains index.php and assets/ (always the real public/ folder).
 */
function public_directory_url_path(): string
{
    $configPath = dirname(__DIR__) . '/config/config.php';
    /** @var array{app?: array{base_url?: string}} $config */
    $config = require $configPath;
    $configured = trim((string) ($config['app']['base_url'] ?? ''));
    if ($configured !== '') {
        return rtrim($configured, '/');
    }

    $sf = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_FILENAME'] ?? ''));
    $sn = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $dir = dirname($sf);

    // Wrapper: Upgrading/index.php — app files are under public/
    if ($sf !== '' && basename($sf) === 'index.php' && is_file($dir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php')) {
        return rtrim(dirname($sn), '/') . '/public';
    }

    // Document root is public/
    return rtrim(dirname($sn), '/');
}

function base_url(string $path = ''): string
{
    $configPath = dirname(__DIR__) . '/config/config.php';
    /** @var array{app?: array{base_url?: string}} $config */
    $config = require $configPath;
    $configured = trim((string) ($config['app']['base_url'] ?? ''));
    $path = ltrim($path, '/');

    if ($configured !== '') {
        $base = rtrim($configured, '/');
        return $path === '' ? $base : $base . '/' . $path;
    }

    $root = public_directory_url_path();
    if ($path === '') {
        return $root === '' ? '/' : $root;
    }
    return $root . '/' . $path;
}

function url(string $route, array $query = []): string
{
    $q = array_merge(['r' => $route], $query);
    $configPath = dirname(__DIR__) . '/config/config.php';
    /** @var array{app?: array{base_url?: string}} $config */
    $config = require $configPath;
    $configured = trim((string) ($config['app']['base_url'] ?? ''));

    if ($configured !== '') {
        $base = rtrim($configured, '/');
        return $base . '/index.php?' . http_build_query($q);
    }

    $root = public_directory_url_path();
    return $root . '/index.php?' . http_build_query($q);
}

function redirect(string $route, array $query = []): void
{
    header('Location: ' . url($route, $query), true, 302);
    exit;
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function is_post(): bool
{
    return request_method() === 'POST';
}

function input(string $key, ?string $default = null): ?string
{
    if (!isset($_POST[$key])) {
        return $default;
    }
    $v = $_POST[$key];
    if (is_array($v)) {
        return $default;
    }
    return (string) $v;
}

function query(string $key, ?string $default = null): ?string
{
    if (!isset($_GET[$key])) {
        return $default;
    }
    $v = $_GET[$key];
    if (is_array($v)) {
        return $default;
    }
    return (string) $v;
}

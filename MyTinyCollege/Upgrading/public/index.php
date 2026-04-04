<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require dirname(__DIR__) . '/includes/router.php';

$route = isset($_GET['r']) ? (string) $_GET['r'] : 'home/index';

$result = app_dispatch($route);
if (isset($result['code'])) {
    http_response_code((int) $result['code']);
}

/** @var array<string, mixed> $data */
$data = $result['data'];
extract($data, EXTR_SKIP);

$viewFile = dirname(__DIR__) . '/views/' . $result['view'] . '.php';
if (!is_file($viewFile)) {
    http_response_code(500);
    echo 'Missing view: ' . e($result['view']);
    exit;
}

ob_start();
require $viewFile;
$content = ob_get_clean();

require dirname(__DIR__) . '/views/layout.php';

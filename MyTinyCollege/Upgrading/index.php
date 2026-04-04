<?php

declare(strict_types=1);

/**
 * Optional entry point when the web root is the Upgrading folder (not public/).
 * For production, prefer serving only public/ as the document root.
 */
require __DIR__ . '/public/index.php';

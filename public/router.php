<?php

declare(strict_types=1);

// Serve only existing assets under the public assets directory during development.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = realpath(__DIR__ . rawurldecode($path));
$assetRoot = realpath(__DIR__ . '/assets') . DIRECTORY_SEPARATOR;
if ($file && str_starts_with($file, $assetRoot) && is_file($file)) {
    return false;
}
require __DIR__ . '/index.php';

<?php

declare(strict_types=1);

use BuligAI\Shared\Http\Response;
use BuligAI\Shared\Security\SessionManager;

$app = require dirname(__DIR__) . '/bootstrap/app.php';

ini_set('display_errors', '0');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; script-src 'self'; "
    . "frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
header('Cache-Control: private, no-store');

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
try {
    $config = require dirname(__DIR__) . '/config/app.php';
    $session = new SessionManager($config['session'], $config['production']);
    $session->start();
    $response = $app->handle($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
    $session->commit();
    $response->send();
} catch (Throwable $error) {
    error_log('BuligAI: request initialization failed: ' . $error::class);
    (new Response('Application configuration is unavailable.', 503, [
        'Content-Type' => 'text/plain; charset=utf-8',
    ]))->send();
}

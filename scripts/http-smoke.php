<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

require dirname(__DIR__) . '/vendor/autoload.php';

$base = rtrim($argv[1] ?? 'http://localhost:8000', '/');
$client = new Client(['base_uri' => $base, 'http_errors' => false, 'allow_redirects' => false, 'timeout' => 10]);
$cookie = '';

try {
    for ($attempt = 0; $attempt < 20; $attempt++) {
        try {
            $client->get('/health');
            break;
        } catch (ConnectException $error) {
            if ($attempt === 19) {
                throw $error;
            }
            usleep(100000);
        }
    }
    $cases = [
        ['GET', '/health', 200], ['GET', '/login', 200], ['GET', '/files', 303],
        ['GET', '/files/delete', 405], ['GET', '/.env', 404], ['GET', '/composer.json', 404],
        ['GET', '/app/Shared/Security/CookieSession.php', 404], ['POST', '/files/delete', 422],
    ];
    $csrf = '';
    foreach ($cases as [$method, $path, $expected]) {
        // Explicit replay also exercises Secure cookies against a local HTTP test server.
        $response = $client->request($method, $path, ['headers' => ['Cookie' => $cookie]]);
        if ($response->getStatusCode() !== $expected) {
            throw new RuntimeException("{$method} {$path}: expected {$expected}, got " . $response->getStatusCode());
        }
        $body = (string) $response->getBody();
        if ($path === '/login') {
            if (!preg_match('/name="_csrf" value="([a-f0-9]{64})"/', $body, $matches)) {
                throw new RuntimeException('Sign-in form is missing its CSRF token.');
            }
            $csrf = $matches[1];
        }
        if ($response->hasHeader('Set-Cookie')) {
            $cookie = explode(';', $response->getHeader('Set-Cookie')[0], 2)[0];
        }
        if ($response->getHeaderLine('Cache-Control') !== 'private, no-store') {
            throw new RuntimeException('Application responses must not be cached.');
        }
        echo "{$method} {$path}: {$expected}\n";
    }
    $response = $client->post('/login', [
        'headers' => ['Cookie' => $cookie],
        'form_params' => ['_csrf' => $csrf, 'email' => 'invalid-email', 'password' => 'test'],
    ]);
    if ($response->getStatusCode() !== 422 || str_contains((string) $response->getBody(), 'Your form expired')) {
        throw new RuntimeException('Session state did not survive between requests.');
    }
    echo "Session/CSRF round trip passed.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'HTTP smoke check failed: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}

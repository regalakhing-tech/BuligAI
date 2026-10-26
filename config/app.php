<?php

declare(strict_types=1);

$env = static function (string $key, string $default = ''): string {
    $value = $_ENV[$key] ?? getenv($key);
    return $value === false ? $default : (string) $value;
};
$isVercel = $env('VERCEL') === '1';

return [
    'name' => $env('APP_NAME', 'BuligAI'),
    'production' => $isVercel || $env('APP_ENV', 'local') === 'production',
    'upload_max_bytes' => ($isVercel ? 3 : 10) * 1024 * 1024,
    'session' => [
        'driver' => $isVercel ? 'cookie' : $env('SESSION_DRIVER', 'file'),
        'key' => $env('APP_KEY'),
    ],
    'supabase' => [
        'url' => $env('SUPABASE_URL'),
        'key' => $env('SUPABASE_PUBLISHABLE_KEY'),
        'bucket' => $env('SUPABASE_STORAGE_BUCKET', 'community-files'),
    ],
    'mail' => [
        'host' => $env('MAIL_HOST'),
        'port' => (int) $env('MAIL_PORT', '587'),
        'username' => $env('MAIL_USERNAME'),
        'password' => $env('MAIL_PASSWORD'),
        'encryption' => $env('MAIL_ENCRYPTION', 'tls'),
        'from_address' => $env('MAIL_FROM_ADDRESS'),
        'from_name' => $env('MAIL_FROM_NAME', 'BuligAI Community'),
    ],
];

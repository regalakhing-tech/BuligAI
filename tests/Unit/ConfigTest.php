<?php

declare(strict_types=1);

namespace BuligAI\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testReadsHostingEnvironmentWithoutADotenvFile(): void
    {
        $previous = getenv('APP_ENV');
        $previousEnv = $_ENV['APP_ENV'] ?? null;
        unset($_ENV['APP_ENV']);
        putenv('APP_ENV=production');
        try {
            $config = require dirname(__DIR__, 2) . '/config/app.php';
            self::assertTrue($config['production']);
        } finally {
            putenv($previous === false ? 'APP_ENV' : 'APP_ENV=' . $previous);
            if ($previousEnv !== null) {
                $_ENV['APP_ENV'] = $previousEnv;
            }
        }
    }

    public function testVercelUsesCookieSessionsAndSmallerUploadBodies(): void
    {
        $previous = getenv('VERCEL');
        putenv('VERCEL=1');
        try {
            $config = require dirname(__DIR__, 2) . '/config/app.php';
            self::assertSame('cookie', $config['session']['driver']);
            self::assertSame(3 * 1024 * 1024, $config['upload_max_bytes']);
        } finally {
            putenv($previous === false ? 'VERCEL' : 'VERCEL=' . $previous);
        }
    }
}

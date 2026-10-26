<?php

declare(strict_types=1);

namespace BuligAI\Tests\Feature;

use BuligAI\Shared\Http\Application;
use BuligAI\Shared\Http\View;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_POST = [];
        $_SERVER['CONTENT_LENGTH'] = 0;
    }

    public function testHealthDoesNotRequireExternalCredentials(): void
    {
        $response = (new Application([]))->handle('GET', '/health');
        self::assertSame(200, $response->status);
        self::assertSame('ok', json_decode($response->body, true)['status']);
    }

    public function testPrivateFilesRedirectGuestsToSignIn(): void
    {
        $response = (new Application([]))->handle('GET', '/files');
        self::assertSame(303, $response->status);
        self::assertSame('/login', $response->headers['Location']);
    }

    public function testPostRequiresCsrfBeforeAnyServiceCall(): void
    {
        $response = (new Application([]))->handle('POST', '/files/delete');
        self::assertSame(422, $response->status);
        self::assertStringContainsString('Your form expired', $response->body);
    }

    public function testGetCannotDeleteAFile(): void
    {
        $response = (new Application([]))->handle('GET', '/files/delete');
        self::assertSame(405, $response->status);
        self::assertSame('POST', $response->headers['Allow']);
    }

    public function testUnknownPathsReturn404(): void
    {
        self::assertSame(404, (new Application([]))->handle('GET', '/unknown')->status);
    }

    public function testViewsEscapeUserContent(): void
    {
        $body = View::render('Shared/Views/error', ['message' => '<script>alert(1)</script>']);
        self::assertStringNotContainsString('<script>alert(1)</script>', $body);
        self::assertStringContainsString('&lt;script&gt;', $body);
    }
}

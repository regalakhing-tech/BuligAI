<?php

declare(strict_types=1);

namespace BuligAI\Tests\Unit;

use BuligAI\Shared\Security\Csrf;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    public function testTokenIsStableWithinTheSession(): void
    {
        $session = [];
        $token = Csrf::token($session);
        self::assertSame($token, Csrf::token($session));
        self::assertSame(64, strlen($token));
        Csrf::verify($session, $token);
    }

    public function testMissingTokensAreRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Csrf::verify([], '');
    }

    public function testIncorrectTokensAreRejected(): void
    {
        $session = [];
        Csrf::token($session);
        $this->expectException(InvalidArgumentException::class);
        Csrf::verify($session, str_repeat('f', 64));
    }
}

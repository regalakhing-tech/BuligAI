<?php

declare(strict_types=1);

namespace BuligAI\Tests\Unit;

use BuligAI\Shared\Security\CookieSession;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CookieSessionTest extends TestCase
{
    private function codec(): CookieSession
    {
        return new CookieSession(base64_encode(str_repeat('a', 32)));
    }

    public function testRoundTripsStateBetweenIndependentRequestsWithoutExposingTokens(): void
    {
        $state = ['auth' => ['access_token' => 'private-access', 'refresh_token' => 'private-refresh']];
        $cookie = $this->codec()->encode($state, 1000);
        self::assertSame($state, $this->codec()->decode($cookie, 1001));
        self::assertStringNotContainsString('private-access', $cookie);
        self::assertStringNotContainsString('private-refresh', base64_decode(strtr($cookie, '-_', '+/')));
    }

    public function testEveryWriteUsesFreshEncryptionRandomness(): void
    {
        self::assertNotSame(
            $this->codec()->encode(['csrf' => 'token'], 1000),
            $this->codec()->encode(['csrf' => 'token'], 1000)
        );
    }

    public function testRejectsTamperingWithoutRestoringAuthentication(): void
    {
        $cookie = $this->codec()->encode(['auth' => ['access_token' => 'secret']], 1000);
        $raw = base64_decode(strtr($cookie, '-_', '+/'));
        $raw[20] = chr(ord($raw[20]) ^ 1);
        self::assertSame([], $this->codec()->decode(rtrim(strtr(base64_encode($raw), '+/', '-_'), '='), 1001));
    }

    public function testRejectsExpiredCookies(): void
    {
        $cookie = $this->codec()->encode(['auth' => ['access_token' => 'secret']], 1000);
        self::assertSame([], $this->codec()->decode($cookie, 1000 + CookieSession::LIFETIME + 1));
    }

    public function testRejectsCookiesEncryptedWithAnotherKey(): void
    {
        $cookie = $this->codec()->encode(['auth' => ['access_token' => 'secret']], 1000);
        $other = new CookieSession(base64_encode(str_repeat('b', 32)));
        self::assertSame([], $other->decode($cookie, 1001));
    }

    public function testRejectsInvalidOrOversizedCookies(): void
    {
        self::assertSame([], $this->codec()->decode('not-base64!!!', 1000));
        self::assertSame([], $this->codec()->decode(str_repeat('a', 5000), 1000));
    }

    public function testRejectsOversizedStateBeforeCreatingAnUnusableCookie(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->codec()->encode(['data' => str_repeat('x', 5000)], 1000);
    }

    public function testRequiresAnExplicitSecretKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CookieSession('');
    }
}

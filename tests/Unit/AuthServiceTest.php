<?php

declare(strict_types=1);

namespace BuligAI\Tests\Unit;

use BuligAI\Modules\Auth\Services\AuthService;
use BuligAI\Shared\Supabase\ApiException;
use BuligAI\Shared\Supabase\SupabaseClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class AuthServiceTest extends TestCase
{
    public function testRefreshesExpiredSessionThenVerifiesUserRemotely(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], json_encode([
                'access_token' => 'fresh-access', 'refresh_token' => 'fresh-refresh', 'expires_in' => 3600,
            ])),
            new Response(200, [], '{"id":"12345678-1234-4234-8234-123456789abc"}'),
        ]));
        $stack->push(Middleware::history($history));
        $auth = new AuthService(new SupabaseClient(
            'https://example.supabase.co',
            'sb_publishable_test',
            new Client(['handler' => $stack])
        ));
        $session = ['access_token' => 'old', 'refresh_token' => 'old-refresh', 'expires_at' => 1];
        $user = $auth->user($session);
        self::assertSame('12345678-1234-4234-8234-123456789abc', $user['id']);
        self::assertSame('fresh-access', $session['access_token']);
        self::assertSame('grant_type=refresh_token', $history[0]['request']->getUri()->getQuery());
        self::assertSame('/auth/v1/user', $history[1]['request']->getUri()->getPath());
        self::assertSame('Bearer fresh-access', $history[1]['request']->getHeaderLine('Authorization'));
    }

    public function testSupabaseErrorsDoNotExposeCredentialsOrRawResponse(): void
    {
        $stack = HandlerStack::create(new MockHandler([
            new Response(401, [], '{"message":"sensitive upstream diagnostic"}'),
        ]));
        $client = new SupabaseClient(
            'https://example.supabase.co',
            'sb_publishable_test',
            new Client(['handler' => $stack])
        );
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Supabase request failed (HTTP 401).');
        $client->request('GET', 'auth/v1/user', 'private-token');
    }

    public function testSignOutRefreshesAnExpiredTokenBeforeRevokingTheSession(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], json_encode([
                'access_token' => 'fresh-access', 'refresh_token' => 'fresh-refresh', 'expires_in' => 3600,
            ])),
            new Response(200, [], '{"id":"12345678-1234-4234-8234-123456789abc"}'),
            new Response(204),
        ]));
        $stack->push(Middleware::history($history));
        $auth = new AuthService(new SupabaseClient(
            'https://example.supabase.co',
            'sb_publishable_test',
            new Client(['handler' => $stack])
        ));
        $session = ['access_token' => 'expired', 'refresh_token' => 'old-refresh', 'expires_at' => 1];
        $auth->signOutSession($session);
        self::assertCount(3, $history);
        self::assertSame('/auth/v1/logout', $history[2]['request']->getUri()->getPath());
        self::assertSame('scope=local', $history[2]['request']->getUri()->getQuery());
        self::assertSame('Bearer fresh-access', $history[2]['request']->getHeaderLine('Authorization'));
    }
}

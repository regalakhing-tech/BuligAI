<?php

declare(strict_types=1);

namespace BuligAI\Modules\Auth\Services;

use BuligAI\Shared\Supabase\ApiException;
use BuligAI\Shared\Supabase\SupabaseClient;

final class AuthService
{
    public function __construct(private readonly SupabaseClient $client)
    {
    }

    public function signIn(string $email, string $password): array
    {
        return $this->session($this->client->json('POST', 'auth/v1/token?grant_type=password', null, [
            'json' => ['email' => $email, 'password' => $password],
        ]));
    }

    public function user(array &$session): array
    {
        if (empty($session['access_token']) || empty($session['refresh_token'])) {
            throw new ApiException(401);
        }
        if (($session['expires_at'] ?? 0) <= time() + 60) {
            $session = $this->session($this->client->json('POST', 'auth/v1/token?grant_type=refresh_token', null, [
                'json' => ['refresh_token' => $session['refresh_token']],
            ]));
        }
        $user = $this->client->json('GET', 'auth/v1/user', $session['access_token']);
        if (!isset($user['id']) || !preg_match('/^[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$/D', $user['id'])) {
            throw new ApiException(401);
        }
        return $user;
    }

    public function signOut(string $token): void
    {
        $this->client->request('POST', 'auth/v1/logout?scope=local', $token);
    }

    public function signOutSession(array &$session): void
    {
        $this->user($session);
        $this->signOut($session['access_token']);
    }

    private function session(array $result): array
    {
        if (empty($result['access_token']) || empty($result['refresh_token'])) {
            throw new ApiException(401);
        }
        return [
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
            'expires_at' => time() + (int) ($result['expires_in'] ?? 3600),
        ];
    }
}

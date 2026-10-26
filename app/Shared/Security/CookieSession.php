<?php

declare(strict_types=1);

namespace BuligAI\Shared\Security;

use InvalidArgumentException;
use JsonException;

final class CookieSession
{
    public const LIFETIME = 7 * 24 * 60 * 60;
    private const MAX_LENGTH = 3800;
    private const CONTEXT = 'buligai-session-v1';
    private readonly string $key;

    public function __construct(string $encodedKey)
    {
        $key = base64_decode($encodedKey, true);
        if ($key === false || strlen($key) !== 32) {
            throw new InvalidArgumentException('APP_KEY must be a base64-encoded, random 32-byte secret.');
        }
        $this->key = $key;
    }

    public function encode(array $state, ?int $now = null): string
    {
        $iv = random_bytes(12);
        $data = json_encode(['exp' => ($now ?? time()) + self::LIFETIME, 'data' => $state], JSON_THROW_ON_ERROR);
        $encrypted = openssl_encrypt($data, 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, $iv, $tag, self::CONTEXT);
        if ($encrypted === false) {
            throw new InvalidArgumentException('The session could not be encrypted.');
        }
        $cookie = rtrim(strtr(base64_encode(chr(1) . $iv . $tag . $encrypted), '+/', '-_'), '=');
        if (strlen($cookie) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Your sign-in session exceeds the supported cookie size.');
        }
        return $cookie;
    }

    public function decode(string $cookie, ?int $now = null): array
    {
        if (strlen($cookie) > self::MAX_LENGTH || !preg_match('/^[a-zA-Z0-9_-]+$/D', $cookie)) {
            return [];
        }
        $raw = base64_decode(strtr($cookie, '-_', '+/'), true);
        if ($raw === false || strlen($raw) < 30 || ord($raw[0]) !== 1) {
            return [];
        }
        $data = openssl_decrypt(
            substr($raw, 29),
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            substr($raw, 1, 12),
            substr($raw, 13, 16),
            self::CONTEXT
        );
        if ($data === false) {
            return [];
        }
        try {
            $payload = json_decode($data, true, 16, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
        if (!is_array($payload) || !is_int($payload['exp'] ?? null) || $payload['exp'] <= ($now ?? time())) {
            return [];
        }
        return is_array($payload['data'] ?? null) ? $payload['data'] : [];
    }
}

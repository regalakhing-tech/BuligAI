<?php

declare(strict_types=1);

namespace BuligAI\Modules\Files\Services;

use BuligAI\Shared\Supabase\SupabaseClient;
use InvalidArgumentException;

final class FileService
{
    public const PAGE_SIZE = 100;

    public function __construct(
        private readonly SupabaseClient $client,
        private readonly UploadValidator $validator,
        private readonly string $bucket
    ) {
        if (!preg_match('/^[a-z0-9-]+$/', $bucket)) {
            throw new InvalidArgumentException('Use a lowercase storage bucket name.');
        }
    }

    public function upload(string $userId, string $token, string $path, string $name, int $error): string
    {
        $this->assertUser($userId);
        $file = $this->validator->validate($path, $error);
        $stem = pathinfo(basename(str_replace('\\', '/', $name)), PATHINFO_FILENAME);
        $stem = trim(substr(preg_replace('/[^a-zA-Z0-9_-]/', '-', $stem), 0, 64), '-');
        $stem = $stem === '' ? 'file' : $stem;
        $object = $userId . '/' . bin2hex(random_bytes(16)) . '-' . $stem . '.' . $file['extension'];
        $stream = fopen($path, 'rb');
        try {
            $this->client->request('POST', 'storage/v1/object/' . $this->bucket . '/' . $object, $token, [
                'headers' => ['Content-Type' => $file['mime'], 'x-upsert' => 'false'],
                'body' => $stream,
            ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
        return $object;
    }

    public function list(string $userId, string $token, int $page = 1): array
    {
        $this->assertUser($userId);
        return $this->client->json('POST', 'storage/v1/object/list/' . $this->bucket, $token, [
            'json' => [
                'prefix' => $userId,
                'limit' => self::PAGE_SIZE,
                'offset' => (max(1, min($page, 100000)) - 1) * self::PAGE_SIZE,
                'sortBy' => ['column' => 'created_at', 'order' => 'desc'],
            ],
        ]);
    }

    public function downloadUrl(string $userId, string $token, string $object): string
    {
        $this->assertOwnedPath($userId, $object);
        $result = $this->client->json('POST', 'storage/v1/object/sign/' . $this->bucket . '/' . $object, $token, [
            'json' => ['expiresIn' => 60],
        ]);
        $url = $this->client->signedStorageUrl((string) ($result['signedURL'] ?? ''));
        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . 'download=' . rawurlencode(substr(basename($object), 33));
    }

    public function delete(string $userId, string $token, string $object): void
    {
        $this->assertOwnedPath($userId, $object);
        $this->client->request('DELETE', 'storage/v1/object/' . $this->bucket, $token, [
            'json' => ['prefixes' => [$object]],
        ]);
    }

    private function assertOwnedPath(string $userId, string $object): void
    {
        $this->assertUser($userId);
        $pattern = '~^' . preg_quote($userId, '~') . '/[a-f0-9]{32}-[a-zA-Z0-9_-]{1,64}\.(pdf|jpg|png|webp)$~D';
        if (!preg_match($pattern, $object)) {
            throw new InvalidArgumentException('Choose a file from your own workspace.');
        }
    }

    private function assertUser(string $userId): void
    {
        if (!preg_match('/^[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$/D', $userId)) {
            throw new InvalidArgumentException('A valid signed-in user is required.');
        }
    }
}

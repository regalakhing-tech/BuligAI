<?php

declare(strict_types=1);

namespace BuligAI\Tests\Unit;

use BuligAI\Modules\Files\Services\FileService;
use BuligAI\Modules\Files\Services\UploadValidator;
use BuligAI\Shared\Supabase\SupabaseClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FileServiceTest extends TestCase
{
    private const USER = '12345678-1234-4234-8234-123456789abc';
    private array $history = [];

    private function service(array $responses): FileService
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->history));
        $client = new SupabaseClient('https://example.supabase.co', 'sb_publishable_test', new Client([
            'handler' => $stack,
        ]));
        return new FileService($client, new UploadValidator(), 'community-files');
    }

    public function testUploadUsesUserTokenAndRandomizedOwnedPath(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'buligai-');
        file_put_contents($path, "%PDF-1.7\nGuide\n%%EOF");
        try {
            $result = $this->service([new Response(200, [], '{}')])
                ->upload(self::USER, 'user-token', $path, '../../Guide.php', UPLOAD_ERR_OK);
            self::assertMatchesRegularExpression(
                '~^' . self::USER . '/[a-f0-9]{32}-Guide\.pdf$~',
                $result
            );
            $request = $this->history[0]['request'];
            self::assertSame('Bearer user-token', $request->getHeaderLine('Authorization'));
            self::assertSame('sb_publishable_test', $request->getHeaderLine('apikey'));
            self::assertSame('false', $request->getHeaderLine('x-upsert'));
            self::assertSame('application/pdf', $request->getHeaderLine('Content-Type'));
        } finally {
            unlink($path);
        }
    }

    public function testCannotDeleteAnotherUsersObject(): void
    {
        $service = $this->service([]);
        $this->expectException(InvalidArgumentException::class);
        try {
            $service->delete(self::USER, 'token', 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa/file.pdf');
        } finally {
            self::assertCount(0, $this->history);
        }
    }

    public function testCannotDownloadTraversalPaths(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service([])->downloadUrl(self::USER, 'token', self::USER . '/../secret.pdf');
    }

    public function testListIsPaginatedWithinTheAuthenticatedUsersFolder(): void
    {
        $result = $this->service([new Response(200, [], '[{"name":"guide.pdf"}]')])
            ->list(self::USER, 'token', 2);
        self::assertSame('guide.pdf', $result[0]['name']);
        $body = json_decode((string) $this->history[0]['request']->getBody(), true);
        self::assertSame(self::USER, $body['prefix']);
        self::assertSame(100, $body['limit']);
        self::assertSame(100, $body['offset']);
    }

    public function testDeleteUsesAnExactOwnedObjectPath(): void
    {
        $object = self::USER . '/' . str_repeat('a', 32) . '-guide.pdf';
        $this->service([new Response(200, [], '[]')])->delete(self::USER, 'token', $object);
        $request = $this->history[0]['request'];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame(['prefixes' => [$object]], json_decode((string) $request->getBody(), true));
    }

    public function testDownloadCreatesAShortLivedLinkInsteadOfProxyingFileBytes(): void
    {
        $object = self::USER . '/' . str_repeat('a', 32) . '-guide.pdf';
        $signed = '/object/sign/community-files/' . $object . '?token=temporary-signature';
        $url = $this->service([new Response(200, [], json_encode(['signedURL' => $signed]))])
            ->downloadUrl(self::USER, 'user-token', $object);
        self::assertSame('https://example.supabase.co/storage/v1' . $signed . '&download=guide.pdf', $url);
        $request = $this->history[0]['request'];
        self::assertSame('POST', $request->getMethod());
        self::assertSame(['expiresIn' => 60], json_decode((string) $request->getBody(), true));
        self::assertSame('Bearer user-token', $request->getHeaderLine('Authorization'));
        self::assertStringNotContainsString('user-token', $url);
    }

    public function testSignedDownloadCannotReferenceAnotherUsersPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service([])->downloadUrl(self::USER, 'token', 'another-user/' . str_repeat('a', 32) . '-file.pdf');
    }

    public function testRefusesAnUnexpectedExternalSignedUrl(): void
    {
        $object = self::USER . '/' . str_repeat('a', 32) . '-guide.pdf';
        $this->expectException(InvalidArgumentException::class);
        $this->service([new Response(200, [], '{"signedURL":"https://untrusted.example/file"}')])
            ->downloadUrl(self::USER, 'token', $object);
    }
}

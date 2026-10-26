<?php

declare(strict_types=1);

namespace BuligAI\Shared\Supabase;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

final class SupabaseClient
{
    private ClientInterface $http;

    public function __construct(
        private readonly string $url,
        private readonly string $publishableKey,
        ?ClientInterface $http = null
    ) {
        if (!filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https') {
            throw new InvalidArgumentException('Set SUPABASE_URL to your HTTPS project URL.');
        }
        if (!str_starts_with($publishableKey, 'sb_publishable_')) {
            throw new InvalidArgumentException('Set SUPABASE_PUBLISHABLE_KEY to a publishable API key.');
        }
        $this->http = $http ?? new Client();
    }

    public function request(
        string $method,
        string $path,
        ?string $accessToken = null,
        array $options = []
    ): ResponseInterface {
        $headers = ['apikey' => $this->publishableKey];
        if ($accessToken !== null) {
            $headers['Authorization'] = 'Bearer ' . $accessToken;
        }
        $options['headers'] = array_merge($options['headers'] ?? [], $headers);
        $options['timeout'] = 30;
        $options['connect_timeout'] = 10;
        $options['http_errors'] = false;
        $options['allow_redirects'] = false;
        try {
            $response = $this->http->request($method, rtrim($this->url, '/') . '/' . ltrim($path, '/'), $options);
        } catch (GuzzleException) {
            throw new ApiException(503);
        }
        if ($response->getStatusCode() >= 300) {
            throw new ApiException($response->getStatusCode());
        }
        return $response;
    }

    public function json(string $method, string $path, ?string $token = null, array $options = []): array
    {
        $body = (string) $this->request($method, $path, $token, $options)->getBody();
        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    public function signedStorageUrl(string $path): string
    {
        if (!str_starts_with($path, '/object/sign/') || str_contains($path, "\r") || str_contains($path, "\n")) {
            throw new InvalidArgumentException('Storage returned an invalid download link.');
        }
        return rtrim($this->url, '/') . '/storage/v1' . $path;
    }
}

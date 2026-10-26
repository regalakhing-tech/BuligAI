<?php

declare(strict_types=1);

namespace BuligAI\Shared\Http;

final class Response
{
    public function __construct(
        public readonly string $body = '',
        public readonly int $status = 200,
        public readonly array $headers = ['Content-Type' => 'text/html; charset=utf-8']
    ) {
    }

    public static function redirect(string $path): self
    {
        return new self('', 303, ['Location' => $path]);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body;
    }
}
